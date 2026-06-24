<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.google.client_id' => 'google-client-id',
            'services.google.client_secret' => 'google-client-secret',
            'services.google.redirect' => 'http://127.0.0.1:8000/auth/google/callback',
        ]);
    }

    public function test_google_redirect_uses_socialite_provider(): void
    {
        Socialite::fake('google');

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://socialite.fake/google/authorize');
    }

    #[DataProvider('roleRedirectProvider')]
    public function test_existing_verified_google_user_is_redirected_for_their_role(string $role, string $routeName): void
    {
        $user = User::factory()->create([
            'email' => "{$role}@example.com",
            'email_verified_at' => null,
        ]);
        $user->assignRole(Role::findOrCreate($role));

        Socialite::fake('google', $this->googleUser($user->email));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route($routeName));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public static function roleRedirectProvider(): array
    {
        return [
            'tourist' => ['tourist', 'home'],
            'host' => ['host', 'host.dashboard'],
            'admin' => ['admin', 'admin.dashboard'],
        ];
    }

    public function test_google_login_does_not_create_an_unknown_account(): void
    {
        Socialite::fake('google', $this->googleUser('unknown@example.com'));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Email Google này chưa được đăng ký. Vui lòng đăng ký tài khoản trước.']);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'unknown@example.com']);
    }

    public function test_unverified_google_email_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'unverified@example.com']);

        Socialite::fake('google', $this->googleUser($user->email, false));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Google chưa xác minh địa chỉ email của tài khoản này.']);

        $this->assertGuest();
    }

    public function test_locked_account_cannot_login_with_google(): void
    {
        $user = User::factory()->create(['email' => 'locked@example.com']);
        $user->assignRole(Role::findOrCreate('tourist'));
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_ACCOUNT_LOCKED));

        Socialite::fake('google', $this->googleUser($user->email));

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.']);

        $this->assertGuest();
    }

    public function test_cancelled_and_invalid_google_sessions_show_clear_errors(): void
    {
        $this->get(route('auth.google.callback', ['error' => 'access_denied']))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Bạn đã hủy đăng nhập Google. Vui lòng thử lại khi cần.']);

        Socialite::fake('google', fn () => throw new InvalidStateException);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Phiên đăng nhập Google không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.']);
    }

    public function test_missing_google_configuration_is_reported_without_exposing_secrets(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
        ]);

        $this->get(route('auth.google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Đăng nhập Google chưa được cấu hình. Vui lòng liên hệ quản trị viên.']);
    }

    public function test_only_login_google_button_is_connected_and_other_social_buttons_remain(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('auth.google.redirect'), false)
            ->assertSee('Facebook');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Google')
            ->assertSee('Facebook')
            ->assertDontSee(route('auth.google.redirect'), false);
    }

    private function googleUser(string $email, bool $verified = true): GoogleUser
    {
        return GoogleUser::fake([
            'name' => 'Google User',
            'email' => $email,
            'verified_email' => $verified,
        ]);
    }
}
