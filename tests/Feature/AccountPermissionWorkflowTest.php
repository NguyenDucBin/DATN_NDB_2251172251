<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\HostRequestStatusNotification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountPermissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_host_registration_uses_existing_permission_tables(): void
    {
        $response = $this->post('/register', [
            'account_type' => 'host',
            'name' => 'Host Pending',
            'email' => 'pending@example.com',
            'phone' => '0900000000',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user = User::where('email', 'pending@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('tourist'));
        $this->assertTrue($user->hasDirectPermission(User::PERMISSION_HOST_PENDING));
        $this->assertSame('pending', $user->hostApprovalStatus());
        $this->assertFalse(Schema::hasColumn('users', 'host_approval_status'));
        $this->assertFalse(Schema::hasColumn('users', 'locked_at'));
    }

    public function test_admin_can_approve_lock_and_unlock_an_account_with_permissions(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_PENDING));

        $this->actingAs($admin)
            ->post(route('admin.users.approve-host', $user))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->hasRole('host'));
        $this->assertSame('approved', $user->hostApprovalStatus());
        Notification::assertSentTo($user, HostRequestStatusNotification::class, function ($notification) use ($user) {
            $mail = $notification->toMail($user);

            return $notification->status === HostRequestStatusNotification::APPROVED
                && $mail->subject === 'Tài khoản Host của bạn đã được phê duyệt'
                && $mail->actionUrl === route('host.login');
        });

        $this->actingAs($admin)
            ->post(route('admin.users.lock', $user))
            ->assertSessionHas('success');

        $this->assertTrue($user->refresh()->isLocked());

        $this->actingAs($admin)
            ->post(route('admin.users.unlock', $user))
            ->assertSessionHas('success');

        $this->assertFalse($user->refresh()->isLocked());
    }

    public function test_admin_can_reject_a_host_request(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_PENDING));

        $this->actingAs($admin)
            ->post(route('admin.users.reject-host', $user))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue($user->hasRole('tourist'));
        $this->assertSame('rejected', $user->hostApprovalStatus());
        Notification::assertSentTo($user, HostRequestStatusNotification::class, function ($notification) use ($user) {
            $mail = $notification->toMail($user);

            return $notification->status === HostRequestStatusNotification::REJECTED
                && $mail->subject === 'Kết quả yêu cầu đăng ký Host'
                && $mail->actionUrl === route('home');
        });
    }

    public function test_processed_host_request_cannot_send_duplicate_notification(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_PENDING));

        $this->actingAs($admin)
            ->post(route('admin.users.approve-host', $user))
            ->assertSessionHas('success');

        $this->actingAs($admin)
            ->post(route('admin.users.approve-host', $user))
            ->assertSessionHas('error');

        Notification::assertSentToTimes($user, HostRequestStatusNotification::class, 1);
    }

    public function test_host_status_is_saved_when_notification_delivery_fails(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));
        $user->givePermissionTo(Permission::findOrCreate(User::PERMISSION_HOST_PENDING));

        $this->mock(Dispatcher::class)
            ->shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMTP unavailable'));

        $this->actingAs($admin)
            ->post(route('admin.users.approve-host', $user))
            ->assertSessionHas('success')
            ->assertSessionHas('warning');

        $user->refresh();
        $this->assertTrue($user->hasRole('host'));
        $this->assertSame('approved', $user->hostApprovalStatus());
    }
}
