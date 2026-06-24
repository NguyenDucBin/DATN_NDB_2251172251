<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FrontendAssetTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_use_local_assets_without_the_removed_script(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('images/static/home-hero.jpg', false)
            ->assertDontSee('build/assets/client/js/main.js', false)
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertDontSee('cdnjs.cloudflare.com', false);
    }

    public function test_admin_dashboard_loads_one_alpine_bundle_and_page_specific_chart(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Xác nhận thao tác')
            ->assertDontSee('cdn.jsdelivr.net/npm/alpinejs', false)
            ->assertDontSee('sweetalert2', false);

        $this->assertTrue(
            str_contains($response->getContent(), 'admin-dashboard-')
                || str_contains($response->getContent(), 'resources/js/admin-dashboard.js')
        );
    }
}
