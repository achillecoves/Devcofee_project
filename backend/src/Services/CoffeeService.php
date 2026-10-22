<?php
namespace App\Services;

use App\Models\Station;
use App\Models\ChargeSession;
use PDO;

class CoffeeService{

    public function __construct(
        private PDO $pdo
    ){}

}