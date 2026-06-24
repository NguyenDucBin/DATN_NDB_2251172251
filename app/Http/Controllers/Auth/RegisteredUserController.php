<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'account_type' => ['required', 'string', 'in:tourist,host'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'regex:/^(0|\+84)[0-9]{9,10}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ], [
            'account_type.required' => 'Vui lòng chọn loại tài khoản.',
            'account_type.in' => 'Loại tài khoản đã chọn không hợp lệ.',
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ và tên không được vượt quá 255 ký tự.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.unique' => 'Email này đã được đăng ký. Vui lòng sử dụng email khác hoặc đăng nhập.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không đúng định dạng Việt Nam.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.password.mixed' => 'Mật khẩu phải có cả chữ hoa và chữ thường.',
            'password.password.letters' => 'Mật khẩu phải có ít nhất một chữ cái.',
            'password.password.numbers' => 'Mật khẩu phải có ít nhất một chữ số.',
            'password.password.symbols' => 'Mật khẩu phải có ít nhất một ký tự đặc biệt.',
            'terms.accepted' => 'Bạn cần đồng ý với điều khoản dịch vụ và chính sách bảo mật.',
        ]);

        $wantsToBeHost = $request->input('account_type') === 'host';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole(Role::findOrCreate('tourist'));

        if ($wantsToBeHost) {
            $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_PENDING));
        }

        event(new Registered($user));

        Auth::login($user);

        if ($wantsToBeHost) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'Yêu cầu đăng ký Host đã được gửi. Admin sẽ duyệt trước khi tài khoản có quyền Host.');
        }

        return redirect()->route('dashboard');
    }
}
