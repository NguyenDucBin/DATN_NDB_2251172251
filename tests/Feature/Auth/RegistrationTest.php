<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'account_type' => 'tourist',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0912345678',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_shows_a_clear_message_for_an_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->from('/register')->post('/register', [
            'account_type' => 'tourist',
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'phone' => '0912345678',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'Email này đã được đăng ký. Vui lòng sử dụng email khác hoặc đăng nhập.',
        ]);

        $this->get('/register')
            ->assertSee('Đăng ký chưa hoàn tất')
            ->assertSee('Email này đã được đăng ký. Vui lòng sử dụng email khác hoặc đăng nhập.');
        $this->assertGuest();
    }

    public function test_registration_password_messages_are_in_vietnamese(): void
    {
        $response = $this->from('/register')->post('/register', [
            'account_type' => 'tourist',
            'name' => 'Weak Password',
            'email' => 'weak@example.com',
            'phone' => '0912345678',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');

        $messages = session('errors')->get('password');
        $this->assertContains('Mật khẩu phải có cả chữ hoa và chữ thường.', $messages);
        $this->assertContains('Mật khẩu phải có ít nhất một chữ số.', $messages);
        $this->assertContains('Mật khẩu phải có ít nhất một ký tự đặc biệt.', $messages);
    }
}
