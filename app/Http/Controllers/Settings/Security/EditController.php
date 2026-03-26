<?php

namespace App\Http\Controllers\Settings\Security;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use OpenApi\Attributes as OA;

class EditController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return Features::canManageTwoFactorAuthentication()
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
                ? [new Middleware('password.confirm')]
                : [];
    }

    #[OA\Get(
        path: '/settings/security',
        operationId: 'settingsSecurityEdit',
        summary: 'Display the security settings page.',
        security: [['laravelSession' => []]],
        tags: ['Settings Security'],
        responses: [
            new OA\Response(response: 200, description: 'Security settings Inertia page returned.'),
            new OA\Response(
                response: 302,
                description: 'Redirect to login or password confirmation when the browser session is missing or password confirmation is required.',
            ),
        ],
    )]
    public function __invoke(TwoFactorAuthenticationRequest $request): Response
    {
        $props = [
            'canManageTwoFactor' => Features::canManageTwoFactorAuthentication(),
        ];

        if (Features::canManageTwoFactorAuthentication()) {
            $request->ensureStateIsValid();

            $props['twoFactorEnabled'] = $request->user()->hasEnabledTwoFactorAuthentication();
            $props['requiresConfirmation'] = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }

        return Inertia::render('settings/Security', $props);
    }
}
