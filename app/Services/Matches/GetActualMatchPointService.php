<?php

namespace App\Services\Matches;

use App\Models\GameMatch;
use App\Models\MatchPoint;

class GetActualMatchPointService
{
    public function get(GameMatch $match): MatchPoint
    {
        return $match->matchPoints()
            ->latest('id')
            ->firstOrFail();
    }
}
