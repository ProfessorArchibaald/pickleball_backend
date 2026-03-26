<?php

namespace Tests\Feature\Filament;

use App\Filament\Auth\Register;
use App\Models\Dictionary\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminBootstrapRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_registration_page_can_be_opened_when_no_users_exist(): void
    {
        $response = $this->get(route('filament.admin.auth.register'));

        $response->assertOk();
    }

    public function test_admin_registration_page_redirects_to_login_when_users_exist(): void
    {
        User::factory()->admin()->create();

        $response = $this->get(route('filament.admin.auth.register'));

        $response->assertRedirect(route('filament.admin.auth.login', absolute: false));
    }

    public function test_first_registered_user_is_assigned_the_admin_role(): void
    {
        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Admin User',
                'last_name' => 'Owner',
                'email' => 'admin@example.com',
                'password' => 'password',
                'passwordConfirmation' => 'password',
            ])
            ->call('register');

        $registeredUser = User::query()->with('role')->sole();

        $this->assertAuthenticatedAs($registeredUser);
        $this->assertSame(UserRole::ADMIN, $registeredUser->role?->name);
        $this->assertSame('Owner', $registeredUser->last_name);
    }

    public function test_registration_is_blocked_after_the_first_user_exists(): void
    {
        User::factory()->admin()->create();

        Livewire::test(Register::class)
            ->assertRedirect(route('filament.admin.auth.login', absolute: false));
    }
}
