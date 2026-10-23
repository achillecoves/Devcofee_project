<?php

namespace App\Models;

class Drink {

    public function __construct(
        public ?int $id,
        public string $name_drink,
        public int $caffeine_mg,
        public int $volume_ml
    ){}
}