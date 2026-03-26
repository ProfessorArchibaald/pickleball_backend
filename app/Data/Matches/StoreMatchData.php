<?php

namespace App\Data\Matches;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class StoreMatchData extends Data
{
    public function __construct(
        #[MapInputName('game_type_id')]
        public int $gameTypeId,
    ) {}
}
