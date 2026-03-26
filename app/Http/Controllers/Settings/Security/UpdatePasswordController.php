<?php

namespace App\Http\Controllers\Settings\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use OpenApi\Attributes as OA;

class UpdatePasswordController extends Controller
{
    #[OA\Put(
        path: '/settings/password',
        operationId: 'settingsSecurityUpdatePassword',
        summary: 'Update the authenticated user password.',
        security: [['laravelSession' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(ref: '#/components/schemas/SettingsPasswordUpdateRequest'),
            ),
        ),
        tags: ['Settings Security'],
        responses: [
            new OA\Response(
                response: 302,
                description: 'Redirect back to the security page. Validation errors are flashed to the session.',
            ),
            new OA\Response(
                response: 429,
                description: 'Too many password update attempts.',
                content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse'),
            ),
        ],
    )]
    public function __invoke(PasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return back();
    }
}
