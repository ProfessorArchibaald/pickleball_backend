<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Dictionary\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_users_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $user = User::factory()->user()->create();

        $response = $this
            ->actingAs($adminUser)
            ->get(UserResource::getUrl());

        $response->assertOk();
        $response->assertSee('Users');
        $response->assertSee($user->email);
    }

    public function test_admin_can_create_user(): void
    {
        $adminUser = User::factory()->admin()->create();
        $userRole = UserRole::query()->where('name', UserRole::USER)->firstOrFail();

        Livewire::actingAs($adminUser)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'John',
                'last_name' => 'Player',
                'email' => 'john@example.com',
                'role_id' => $userRole->id,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserResource::getUrl('index'));

        $this->assertDatabaseHas(User::class, [
            'email' => 'john@example.com',
            'name' => 'John',
            'last_name' => 'Player',
            'role_id' => $userRole->id,
            'is_blocked' => false,
        ]);
    }

    public function test_admin_can_edit_user(): void
    {
        $adminUser = User::factory()->admin()->create();
        $userRole = UserRole::query()->where('name', UserRole::USER)->firstOrFail();
        $targetUser = User::factory()->user()->create();

        Livewire::actingAs($adminUser)
            ->test(EditUser::class, ['record' => $targetUser->getRouteKey()])
            ->fillForm([
                'name' => 'Jane',
                'last_name' => 'Updated',
                'email' => 'jane@example.com',
                'role_id' => $userRole->id,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(User::class, [
            'id' => $targetUser->id,
            'name' => 'Jane',
            'last_name' => 'Updated',
            'email' => 'jane@example.com',
        ]);
    }

    public function test_admin_can_block_and_unblock_user_from_edit_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $targetUser = User::factory()->user()->create([
            'is_blocked' => false,
        ]);

        Livewire::actingAs($adminUser)
            ->test(EditUser::class, ['record' => $targetUser->getRouteKey()])
            ->assertActionExists('toggleBlock')
            ->callAction('toggleBlock');

        $this->assertTrue($targetUser->fresh()->isBlocked());

        Livewire::actingAs($adminUser)
            ->test(EditUser::class, ['record' => $targetUser->getRouteKey()])
            ->callAction('toggleBlock');

        $this->assertFalse($targetUser->fresh()->isBlocked());
    }

    public function test_delete_action_is_not_available_for_users(): void
    {
        $adminUser = User::factory()->admin()->create();
        $targetUser = User::factory()->user()->create();

        Livewire::actingAs($adminUser)
            ->test(EditUser::class, ['record' => $targetUser->getRouteKey()])
            ->assertActionDoesNotExist('delete');
    }
}
