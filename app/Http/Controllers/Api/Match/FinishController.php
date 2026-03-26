<?php

namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatchResource;
use App\Models\GameMatch;
use App\Services\Matches\FinishMatchService;
use Illuminate\Http\JsonResponse;

class FinishController extends Controller
{
    public function __invoke(GameMatch $match, FinishMatchService $finishMatchService): JsonResponse
    {
        return MatchResource::make($finishMatchService->finish($match))->response();
    }
}
