<?php

namespace App\Http\Controllers\Api\Match;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreMatchRequest;
use App\Http\Resources\MatchResource;
use App\Services\Matches\CreateMatchService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    public function __invoke(StoreMatchRequest $request, CreateMatchService $createMatchService): JsonResponse
    {
        $match = $createMatchService->create($request->toData());

        return MatchResource::make($match)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
