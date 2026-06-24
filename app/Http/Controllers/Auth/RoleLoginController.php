<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoleLoginController extends Controller
{
    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $this->validateCredentials($request);
        $this->ensureIsNotRateLimited($request, 'admin');

        if (Auth::check()) {
            $this->logoutCurrentSession($request);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($lockedResponse = $this->redirectIfLocked($request)) {
                return $lockedResponse;
            }

            if (Auth::user()->hasRole('admin')) {
                RateLimiter::clear($this->throttleKey($request, 'admin'));
                return redirect()->route('admin.dashboard');
            }

            $this->logoutCurrentSession($request);
            RateLimiter::hit($this->throttleKey($request, 'admin'));

            return back()->withErrors([
                'email' => 'Tài khoản này không có quyền truy cập hệ thống quản trị.',
            ])->onlyInput('email');
        }

        RateLimiter::hit($this->throttleKey($request, 'admin'));

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    public function showHostLogin()
    {
        if (Auth::check() && Auth::user()->hasRole('host')) {
            return redirect()->route('host.dashboard');
        }

        return view('auth.host-login');
    }

    public function hostLogin(Request $request)
    {
        $credentials = $this->validateCredentials($request);
        $this->ensureIsNotRateLimited($request, 'host');

        if (Auth::check()) {
            $this->logoutCurrentSession($request);
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($lockedResponse = $this->redirectIfLocked($request)) {
                return $lockedResponse;
            }

            $user = Auth::user();

            if ($user->hasRole('host')) {
                RateLimiter::clear($this->throttleKey($request, 'host'));
                return redirect()->route('host.dashboard');
            }

            $message = match ($user->hostApprovalStatus()) {
                'pending' => 'Yêu cầu đăng ký Host của bạn đang chờ admin duyệt.',
                'rejected' => 'Yêu cầu đăng ký Host của bạn đã bị từ chối. Vui lòng liên hệ quản trị viên nếu cần hỗ trợ.',
                default => 'Tài khoản này không có quyền truy cập Kênh Chủ Nhà.',
            };

            $this->logoutCurrentSession($request);
            RateLimiter::hit($this->throttleKey($request, 'host'));

            return back()->withErrors([
                'email' => $message,
            ])->onlyInput('email');
        }

        RateLimiter::hit($this->throttleKey($request, 'host'));

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    private function redirectIfLocked(Request $request)
    {
        if (! Auth::user()?->isLocked()) {
            return null;
        }

        $this->logoutCurrentSession($request);

        return back()->withErrors([
            'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
        ])->onlyInput('email');
    }

    private function validateCredentials(Request $request): array
    {
        return $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);
    }

    private function logoutCurrentSession(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    private function ensureIsNotRateLimited(Request $request, string $portal): void
    {
        $key = $this->throttleKey($request, $portal);

        if (! RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

        event(new Lockout($request));
        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => "Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau {$seconds} giây.",
        ]);
    }

    private function throttleKey(Request $request, string $portal): string
    {
        return $portal.'|'.Str::transliterate(Str::lower($request->string('email'))).'|'.$request->ip();
    }
}
