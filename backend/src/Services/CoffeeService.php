<?php
namespace App\Services;

use App\Models\Station;
use App\Models\ChargeSession;
use PDO;

class CoffeeService{

    public function __construct(
        private PDO $pdo
    ){}

    public function getAllDrinks()
    {

        $req = $this->pdo->query("SELECT id, drinks.name, caffeine_mg, volume_ml FROM drinks");

        $rows = $req->fetchAll();

        return array_map(fn($r) => new Drink(
            (int) $r->id,
            $r->name,
            (int) $r->caffeine_mg,
            (int) $r->volume_ml
        ), $rows);
    }

    public function getDrinkById(int $id): ?Drink
    {
      $req = $this->pdo->prepare("SELECT id, name_drink, caffeine_mg, volume_ml FROM drinks");
      $req->bindValue(":id", $id, PDO::PARAM_INT);
      $req->execute();

      $r = $req->fetch();
      if(!$r) return null;

      return new Drink(
        (int) $r->id,
        $r->name,
        (int) $r->caffeine_mg,
        (int) $r->volume_ml
      );
    }

    public function createDrink(string $name_drink, int $caffeine_mg, int $volume_ml): Drink
    {
      $req = $this->pdo->prepare("INSERT INTO drinks(name_drink, caffeine_mg, volume_ml) VALUES(:name_drink, :caffeine_mg, :volume_ml)");
      
      $req->bindValue(":name_drink", $name_drink, PDO::PARAM_STR);
      $req->bindValue(":caffeine_mg", $caffeine_mg, PDO::PARAM_INT);
      $req->bindValue(":volume_ml", $volume_ml, PDO::PARAM_INT);
      $req->execute();
      $id = (int) $this->pdo->lastInsertId();

      return new Drink(
        $id,
        $name_drink,
        $caffeine_mg,
        $volume_ml
      );

    }

}