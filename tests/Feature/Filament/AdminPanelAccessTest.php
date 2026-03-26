<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_can_open_the_filament_dashboard(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get(route('filament.admin.pages.dashboard'));

        $response->assertOk();
    }

    public function test_admin_users_can_see_the_swagger_navigation_link_in_settings(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get(route('filament.admin.pages.dashboard'));

        $response
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee('Swagger')
            ->assertSee(route('l5-swagger.default.api', absolute: false), false);
    }

    public function test_non_admin_users_cannot_open_the_filament_dashboard(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get(route('filament.admin.pages.dashboard'));

        $response->assertForbidden();
    }

    public function test_blocked_admin_users_cannot_open_the_filament_dashboard(): void
    {
        $adminUser = User::factory()->admin()->blocked()->create();

        $response = $this->actingAs($adminUser)->get(route('filament.admin.pages.dashboard'));

        $response->assertForbidden();
    }
}
