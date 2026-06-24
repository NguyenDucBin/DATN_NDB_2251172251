<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return $this->loginError('Đăng nhập Google chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if (! $this->isConfigured()) {
            return $this->loginError('Đăng nhập Google chưa được cấu hình. Vui lòng liên hệ quản trị viên.');
        }

        if ($request->filled('error')) {
            return $this->loginError('Bạn đã hủy đăng nhập Google. Vui lòng thử lại khi cần.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return $this->loginError('Phiên đăng nhập Google không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.');
        } catch (Throwable $exception) {
            Log::warning('Google OAuth callback failed.', [
                'exception' => $exception::class,
            ]);

            return $this->loginError('Google chưa thể xác thực tài khoản. Vui lòng thử lại sau.');
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $emailVerified = filter_var(
            data_get($googleUser->user, 'verified_email', false),
            FILTER_VALIDATE_BOOL
        );

        if ($email === '' || ! $emailVerified) {
            return $this->loginError('Google chưa xác minh địa chỉ email của tài khoản này.');
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            return $this->loginError('Email Google này chưa được đăng ký. Vui lòng đăng ký tài khoản trước.');
        }

        if ($user->isLocked()) {
            return $this->loginError('Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.');
        }

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        $request->session()->regenerate();

        return match (true) {
            $user->hasRole('admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('host') => redirect()->route('host.dashboard'),
            default => redirect()->route('home'),
        };
    }

    private function isConfigured(): bool
    {
        return filled(config('services.google.client_id'))
            && filled(config('services.google.client_secret'))
            && filled(config('services.google.redirect'));
    }

    private function loginError(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['email' => $message]);
    }
}
