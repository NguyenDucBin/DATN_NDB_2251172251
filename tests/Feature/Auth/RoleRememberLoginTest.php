<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleRememberLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_can_be_remembered(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $response = $this->post(route('admin.login.submit'), [
            'email' => $admin->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertCookie(Auth::guard('web')->getRecallerName());
        $this->assertNotNull($admin->refresh()->remember_token);
    }

    public function test_host_login_can_be_remembered(): void
    {
        $host = User::factory()->create();
        $host->assignRole(Role::findOrCreate('host'));

        $response = $this->post(route('host.login.submit'), [
            'email' => $host->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('host.dashboard'));
        $response->assertCookie(Auth::guard('web')->getRecallerName());
        $this->assertNotNull($host->refresh()->remember_token);
    }
}
