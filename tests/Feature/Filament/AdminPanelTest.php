<?php

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_filament_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('filament.admin.auth.login'));

        $response->assertOk();
    }

    public function test_filament_login_screen_shows_register_button_when_no_users_exist(): void
    {
        $response = $this->get(route('filament.admin.auth.login'));

        $response->assertSee(route('filament.admin.auth.register'));
    }

    public function test_filament_login_screen_hides_register_button_when_users_exist(): void
    {
        User::factory()->admin()->create();

        $response = $this->get(route('filament.admin.auth.login'));

        $response->assertDontSee(route('filament.admin.auth.register'));
    }

    public function test_guests_are_redirected_to_the_filament_login_screen(): void
    {
        $response = $this->get(route('filament.admin.pages.dashboard'));

        $response->assertRedirect(route('filament.admin.auth.login', absolute: false));
    }
}
