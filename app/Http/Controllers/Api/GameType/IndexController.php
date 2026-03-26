<?php

namespace App\Http\Controllers\Api\GameType;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameTypeResource;
use App\Models\Dictionary\Game\GameType;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class IndexController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        return GameTypeResource::collection(
            GameType::query()
                ->orderBy('id')
                ->get(),
        );
    }
}
