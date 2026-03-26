<?php

namespace App\Http\Controllers\Settings\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use OpenApi\Attributes as OA;

class EditController extends Controller
{
    #[OA\Get(
        path: '/settings/profile',
        operationId: 'settingsProfileEdit',
        summary: 'Display the profile settings page.',
        security: [['laravelSession' => []]],
        tags: ['Settings Profile'],
        responses: [
            new OA\Response(response: 200, description: 'Profile settings Inertia page returned.'),
            new OA\Response(response: 302, description: 'Redirect to login when the browser session is missing.'),
        ],
    )]
    public function __invoke(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ]);
    }
}
