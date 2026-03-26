<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameTypeResource;
use App\Models\Dictionary\GameType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GameTypeController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return GameTypeResource::collection(
            GameType::query()
                ->orderBy('id')
                ->get(),
        );
    }
}
