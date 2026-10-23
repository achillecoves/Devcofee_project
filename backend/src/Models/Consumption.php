<?php
namespace App\Models;

class Consumption{
    public function __construct(
    public ?int $id,
    public int $drinkId,
    public string $developerName,
    public ?string $consumedAt = null
  ){}
}