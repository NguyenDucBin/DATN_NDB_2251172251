<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\HostRequestStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');

        $usersQuery = User::with(['roles', 'permissions'])->latest();

        match ($filter) {
            'pending_hosts' => $usersQuery->whereHas('permissions', fn ($query) => $query->where('name', User::PERMISSION_HOST_PENDING)),
            'approved_hosts' => $usersQuery->whereHas('roles', fn ($query) => $query->where('name', 'host')),
            'locked' => $usersQuery->whereHas('permissions', fn ($query) => $query->where('name', User::PERMISSION_ACCOUNT_LOCKED)),
            default => null,
        };

        $users = $usersQuery->paginate(15)->withQueryString();

        $stats = [
            'all' => User::count(),
            'pending_hosts' => User::whereHas('permissions', fn ($query) => $query->where('name', User::PERMISSION_HOST_PENDING))->count(),
            'approved_hosts' => User::whereHas('roles', fn ($query) => $query->where('name', 'host'))->count(),
            'locked' => User::whereHas('permissions', fn ($query) => $query->where('name', User::PERMISSION_ACCOUNT_LOCKED))->count(),
        ];

        return view('admin.users.index', compact('users', 'filter', 'stats'));
    }

    public function create()
    {
        $roles = Role::all();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'Đã tạo người dùng mới thành công.');
    }

    public function edit(User $user)
    {
        if ($user->hasRole('admin') && $user->id !== auth()->id()) {
            abort(403);
        }

        $roles = Role::all();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|exists:roles,name',
        ]);

        if ($user->hasRole('admin') && $user->id !== auth()->id()) {
            abort(403);
        }

        if ($user->hasRole('admin') && $validated['role'] !== 'admin') {
            return back()->with('error', 'Không thể thay đổi vai trò của tài khoản Admin.')->withInput();
        }

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);
        $this->clearHostRequestPermissions($user);

        return redirect()->route('admin.users.index')->with('success', 'Đã cập nhật thông tin người dùng thành công.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        if ($user->hasRole('admin')) {
            return back()->with('error', 'Không thể xóa tài khoản Admin.');
        }

        if ($user->bookings()->exists() || $user->hostedTours()->exists()) {
            return back()->with('error', 'Tài khoản đã có dữ liệu tour hoặc booking. Hãy khóa tài khoản thay vì xóa.');
        }

        $avatar = $user->avatar;
        $user->syncRoles([]);
        $user->syncPermissions([]);

        $user->delete();

        if ($avatar && ! filter_var($avatar, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete(ltrim(str_starts_with($avatar, 'storage/') ? substr($avatar, 8) : $avatar, '/'));
        }

        return redirect()->route('admin.users.index')->with('success', 'Đã xóa người dùng.');
    }

    public function approveHost(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Không thể cấp quyền Host cho tài khoản admin.');
        }

        if (! $user->hasPendingHostRequest()) {
            return redirect()->back()->with('error', 'Yêu cầu Host này đã được xử lý hoặc không còn ở trạng thái chờ duyệt.');
        }

        Role::findOrCreate('host');
        $this->clearHostRequestPermissions($user);
        $user->syncRoles(['host']);

        $response = redirect()->back()->with('success', "Đã duyệt quyền Host cho {$user->name}.");

        if (! $this->sendHostStatusNotification($user, HostRequestStatusNotification::APPROVED)) {
            $response->with('warning', 'Quyền Host đã được cập nhật nhưng email thông báo chưa gửi được. Vui lòng kiểm tra cấu hình Gmail.');
        }

        return $response;
    }

    public function rejectHost(User $user)
    {
        if ($user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Không thể từ chối quyền Host của tài khoản admin.');
        }

        if (! $user->hasPendingHostRequest()) {
            return redirect()->back()->with('error', 'Yêu cầu Host này đã được xử lý hoặc không còn ở trạng thái chờ duyệt.');
        }

        Role::findOrCreate('tourist');
        $this->clearHostRequestPermissions($user);
        $user->syncRoles(['tourist']);
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_REJECTED));

        $response = redirect()->back()->with('success', "Đã từ chối yêu cầu Host của {$user->name}.");

        if (! $this->sendHostStatusNotification($user, HostRequestStatusNotification::REJECTED)) {
            $response->with('warning', 'Yêu cầu Host đã được từ chối nhưng email thông báo chưa gửi được. Vui lòng kiểm tra cấu hình Gmail.');
        }

        return $response;
    }

    public function lock(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Bạn không thể khóa tài khoản của chính mình.');
        }

        if ($user->hasRole('admin')) {
            return redirect()->back()->with('error', 'Không thể khóa tài khoản admin.');
        }

        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_ACCOUNT_LOCKED));

        return redirect()->back()->with('success', "Đã khóa tài khoản {$user->name}.");
    }

    public function unlock(User $user)
    {
        $permission = Permission::where('name', User::PERMISSION_ACCOUNT_LOCKED)
            ->where('guard_name', 'web')
            ->first();

        if ($permission && $user->hasDirectPermission($permission)) {
            $user->revokePermissionTo($permission);
        }

        return redirect()->back()->with('success', "Đã mở khóa tài khoản {$user->name}.");
    }

    private function clearHostRequestPermissions(User $user): void
    {
        $permissions = Permission::where('guard_name', 'web')
            ->whereIn('name', [User::PERMISSION_HOST_PENDING, User::PERMISSION_HOST_REJECTED])
            ->get();

        foreach ($permissions as $permission) {
            if ($user->hasDirectPermission($permission)) {
                $user->revokePermissionTo($permission);
            }
        }
    }

    private function sendHostStatusNotification(User $user, string $status): bool
    {
        try {
            $user->notify(new HostRequestStatusNotification($status));

            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }
}
