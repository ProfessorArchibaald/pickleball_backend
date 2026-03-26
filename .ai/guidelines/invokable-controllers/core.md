# Invokable Controllers

Controllers in this application follow the **single-action (invokable) pattern**. Each controller handles exactly one action via a `__invoke()` method.

## Directory Structure

Group controllers by domain into subdirectories. The subdirectory name is the resource/domain, and the file name is the action:

```
app/Http/Controllers/
├── Api/
│   ├── Auth/
│   │   ├── LoginController.php
│   │   └── LogoutController.php
│   ├── Match/
│   │   ├── StoreController.php
│   │   └── FinishController.php
│   └── GameType/
│       └── IndexController.php
└── Settings/
    ├── Profile/
    │   ├── EditController.php
    │   ├── UpdateController.php
    │   └── DeleteController.php
    └── Security/
        ├── EditController.php
        └── UpdatePasswordController.php
```

## Controller Structure

Every controller has a single public `__invoke()` method with full type hints and return type:

```php
namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;

class StoreController extends Controller
{
    public function __invoke(StoreMatchRequest $request, CreateMatchService $service): JsonResponse
    {
        // single action logic
    }
}
```

## Route Registration

Reference the controller class directly — no method array syntax:

```php
// Correct
Route::post('/matches', StoreController::class);

// Wrong
Route::post('/matches', [MatchController::class, 'store']);
```

When two controllers in different subdirectories share a class name (e.g. `EditController`), alias the import:

```php
use App\Http\Controllers\Settings\Profile\EditController as EditProfileController;
use App\Http\Controllers\Settings\Security\EditController as EditSecurityController;

Route::get('settings/profile', EditProfileController::class);
Route::get('settings/security', EditSecurityController::class);
```

## Artisan

Use `php artisan make:controller` with the full subdirectory path:

```bash
php artisan make:controller Api/Match/StoreController --invokable --no-interaction
```
