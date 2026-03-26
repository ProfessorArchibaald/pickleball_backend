# OpenAPI Documentation

This application uses `darkaonline/l5-swagger` with PHP 8 attributes (`OpenApi\Attributes as OA`) to generate OpenAPI 3.0 docs. **Every controller, form request, and resource must have OpenAPI attributes.** Documentation lives alongside the code it describes.

## Central Spec

Global metadata (API info, tags, security schemes, shared schemas) lives in `app/OpenApi/OpenApiSpec.php`.

- Add a new `#[OA\Tag]` here when introducing a new domain group.
- Add reusable response schemas here (e.g. `MessageResponse`, `ValidationErrorResponse`).
- Never duplicate tag names — reference existing tags from the controller.

## Controllers

Add an `#[OA\Get]`, `#[OA\Post]`, `#[OA\Patch]`, `#[OA\Put]`, or `#[OA\Delete]` attribute to the `__invoke()` method of every invokable controller:

```php
use OpenApi\Attributes as OA;

class StoreController extends Controller
{
    #[OA\Post(
        path: '/api/matches',
        operationId: 'apiMatchStore',
        tags: ['API Match'],
        summary: 'Create a new match.',
        security: [['sanctumBearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ApiStoreMatchRequest'),
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Match created.',
                content: new OA\JsonContent(ref: '#/components/schemas/MatchData'),
            ),
            new OA\Response(response: 422, description: 'Validation error.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function __invoke(StoreMatchRequest $request, CreateMatchService $service): JsonResponse
```

**Required fields:** `path`, `operationId`, `tags`, `summary`, `responses`.

**operationId convention:** camelCase, prefix with the tag group — e.g. `apiAuthLogin`, `settingsProfileUpdate`.

**Security:**
- API routes (Sanctum): `security: [['sanctumBearer' => []]]`
- Settings/web routes (session): `security: [['laravelSession' => []]]`
- Public routes: omit `security`.

**Settings form routes** use `application/x-www-form-urlencoded`, not JSON:
```php
requestBody: new OA\RequestBody(
    required: true,
    content: new OA\MediaType(
        mediaType: 'application/x-www-form-urlencoded',
        schema: new OA\Schema(ref: '#/components/schemas/SettingsProfileUpdateRequest'),
    ),
),
```

## Form Requests

Add `#[OA\Schema]` to every `FormRequest` class:

```php
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ApiStoreMatchRequest',
    type: 'object',
    required: ['game_type_id'],
    properties: [
        new OA\Property(property: 'game_type_id', type: 'integer', example: 1),
    ],
)]
class StoreMatchRequest extends FormRequest
```

**Schema naming convention:** `Api{Name}Request` for API requests, `Settings{Name}Request` for settings requests.

## Resources

Add `#[OA\Schema]` to every `JsonResource` class:

```php
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MatchData',
    type: 'object',
    required: ['id', 'game_type_id', 'is_finished'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'game_type_id', type: 'integer', example: 2),
        new OA\Property(property: 'is_finished', type: 'boolean', example: false),
    ],
)]
class MatchResource extends JsonResource
```

**Schema naming convention:** `{Model}Data` — e.g. `UserData`, `MatchData`, `GameTypeData`.

## Verification

After any controller, request, or resource change, run the documentation test to confirm the spec generates correctly:

```bash
php artisan test --compact tests/Feature/Documentation/
```

The test asserts that all expected routes and schemas are present in the generated `storage/api-docs/api-docs.json`.

## Do Not Commit Generated Files

`storage/api-docs/api-docs.json` is generated at runtime — do not commit it.
