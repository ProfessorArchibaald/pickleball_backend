# API-First Development

This application is consumed by two clients: an iPhone mobile app and a Vue SPA frontend. Both communicate exclusively via the JSON API. Every new entity **must** expose an API controller.

## Controller Generation

- Always create a versioned API controller under `app/Http/Controllers/Api/V1/`.
- Use `php artisan make:controller Api/V1/{Name}Controller --api --no-interaction` to scaffold.
- Never return Inertia responses from API controllers — always return JSON.

## API Resources

- Always create an Eloquent API Resource for every new model: `php artisan make:resource {Name}Resource --no-interaction`.
- Use the resource in all controller responses instead of returning models directly.
- For collections, use `{Name}Resource::collection($items)`.

## Routing

- Register all API routes in `routes/api.php` inside a versioned prefix group:

```php
Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::apiResource('examples', Api\V1\ExampleController::class);
});
```

## Authentication

- Protect routes that require authentication with the `sanctum` middleware: `->middleware('auth:sanctum')`.
- Public read-only endpoints (e.g. `index`, `show`) may omit auth middleware when appropriate.

## Response Format

- Return `201 Created` for `store`, `200 OK` for `update`/`show`/`index`, `204 No Content` for `destroy`.
- Wrap errors in a consistent JSON structure: `{ "message": "...", "errors": {} }`.
