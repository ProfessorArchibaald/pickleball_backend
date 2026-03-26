<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Dictionary\UserRoles\Pages\CreateUserRole;
use App\Filament\Resources\Dictionary\UserRoles\Pages\EditUserRole;
use App\Filament\Resources\Dictionary\UserRoles\UserRoleResource;
use App\Models\Dictionary\UserRole;
use App\Models\User;
use Closure;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class UserRoleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_admin_can_open_user_roles_list_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $role = UserRole::factory()->create(['name' => 'Manager']);

        $response = $this
            ->actingAs($adminUser)
            ->get(UserRoleResource::getUrl());

        $response->assertOk();
        $response->assertSee('User Roles');
        $response->assertSee($role->name);
    }

    public function test_admin_can_create_user_role(): void
    {
        $adminUser = User::factory()->admin()->create();

        Livewire::actingAs($adminUser)
            ->test(CreateUserRole::class)
            ->fillForm([
                'name' => 'Coach',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(UserRoleResource::getUrl('index'));

        $this->assertDatabaseHas(UserRole::class, [
            'name' => 'Coach',
        ]);
    }

    public function test_admin_can_edit_user_role(): void
    {
        $adminUser = User::factory()->admin()->create();
        $role = UserRole::factory()->create(['name' => 'Coach']);

        Livewire::actingAs($adminUser)
            ->test(EditUserRole::class, ['record' => $role->getRouteKey()])
            ->fillForm([
                'name' => 'Referee',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas(UserRole::class, [
            'id' => $role->id,
            'name' => 'Referee',
        ]);
    }

    public function test_admin_can_delete_user_role_from_edit_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $role = UserRole::factory()->create(['name' => 'Coach']);

        Livewire::actingAs($adminUser)
            ->test(EditUserRole::class, ['record' => $role->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertDatabaseMissing(UserRole::class, [
            'id' => $role->id,
        ]);
    }

    public function test_admin_cannot_delete_default_user_role_from_edit_page(): void
    {
        $adminUser = User::factory()->admin()->create();
        $role = UserRole::query()->where('name', UserRole::ADMIN)->firstOrFail();

        Livewire::actingAs($adminUser)
            ->test(EditUserRole::class, ['record' => $role->getRouteKey()])
            ->assertActionHidden(DeleteAction::class)
            ->assertFormFieldDisabled('name');
    }

    public function test_default_user_roles_cannot_be_renamed_or_deleted(): void
    {
        $role = UserRole::query()->where('name', UserRole::USER)->firstOrFail();

        $renameException = $this->captureValidationException(
            fn (): bool => $role->update(['name' => 'Member']),
        );

        $this->assertSame(
            ['The default user roles cannot be renamed.'],
            $renameException->errors()['name'],
        );

        $role->refresh();

        $deleteException = $this->captureValidationException(
            fn (): ?bool => $role->delete(),
        );

        $this->assertSame(
            ['The default user roles cannot be deleted.'],
            $deleteException->errors()['role'],
        );

        $this->assertDatabaseHas(UserRole::class, [
            'id' => $role->id,
            'name' => UserRole::USER,
        ]);
    }

    private function captureValidationException(Closure $callback): ValidationException
    {
        try {
            $callback();
        } catch (ValidationException $exception) {
            return $exception;
        }

        $this->fail('Expected a validation exception to be thrown.');
    }
}
