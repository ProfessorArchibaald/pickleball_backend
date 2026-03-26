<?php

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class DeleteController extends Controller
{
    #[OA\Delete(
        path: '/settings/profile',
        operationId: 'settingsProfileDelete',
        summary: 'Delete the authenticated user account.',
        security: [['laravelSession' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/x-www-form-urlencoded',
                schema: new OA\Schema(ref: '#/components/schemas/SettingsProfileDeleteRequest'),
            ),
        ),
        tags: ['Settings Profile'],
        responses: [
            new OA\Response(
                response: 302,
                description: 'Redirect to the home page on success or back to the profile page when validation fails.',
            ),
        ],
    )]
    public function __invoke(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
