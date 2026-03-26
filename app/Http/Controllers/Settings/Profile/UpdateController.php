<?php

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use OpenApi\Attributes as OA;

class UpdateController extends Controller
{
    #[OA\Patch(
        path: '/settings/profile',
        operationId: 'settingsProfileUpdate',
        summary: 'Update the authenticated user profile.',
        security: [['laravelSession' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(ref: '#/components/schemas/SettingsProfileUpdateRequest'),
            ),
        ),
        tags: ['Settings Profile'],
        responses: [
            new OA\Response(
                response: 302,
                description: 'Redirect back to the profile page. Validation errors are flashed to the session.',
            ),
        ],
    )]
    public function __invoke(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('profile.edit');
    }
}
