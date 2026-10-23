<?php

namespace App\Models;

class HistoricalConsumption
{
    public function __construct(
        public int $id,
        public string $developerName,
        public string $drinkName,
        public int $caffeineMg,
        public string $consumedAt
    ) {}
}