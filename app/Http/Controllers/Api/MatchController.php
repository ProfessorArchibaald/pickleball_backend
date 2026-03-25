<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MatchController extends Controller
{
    public function store(): JsonResponse
    {
        $match = GameMatch::query()->create();

        return MatchResource::make($match)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function finish(GameMatch $match): JsonResponse
    {
        if ($match->finished_at === null) {
            $match->forceFill([
                'finished_at' => now(),
            ])->save();
        }

        return MatchResource::make($match->fresh())->response();
    }
}
