<?php
namespace App\Services;

use App\Models\Drink;
use App\Models\Consumption;
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

    public function getDrinkCaffeine(int $drinkId): ?int{
        $req = $this->pdo->prepare("SELECT caffeine_mg FROM drinks WHERE id = :id");
        $req->bindValue(":id", $drinkId, PDO::PARAM_INT);
        $req->execute();

        $value = $req->fetchColumn();
        return $value === false ? null : (int) $value;
    }

    public function getLastConsumptions(): array{
      $req = $this->pdo->prepare("SELECT drink_id, developer_name, id, consumed_at FROM consumptions ORDER BY id DESC LIMIT 20");

      $req->execute();
      $consumptions = $req->fetchAll(PDO::FETCH_OBJ);

      return array_map(fn($c) => new Consumption(
        id: (int) $c->id,
        drinkId: (int) $c->drink_id,
        developerName: (string) $c->developer_name,
        consumedAt: (string) $c->consumed_at
      ), $consumptions);
    }

    public function createConsumption(
      int $drinkId,
      string $developerName
    ): Consumption{
      $req = $this->pdo->prepare("INSERT INTO consumptions(drink_id, developer_name) VALUES(:drinkId, :developerName)");
      
      $req->bindValue(":drinkId", $drinkId, PDO::PARAM_INT);
      $req->bindValue(":developerName", $developerName, PDO::PARAM_STR);
      $req->execute();

      $id = (int) $this->pdo->lastInsertId();

      $select = $this->pdo->prepare("SELECT consumed_at FROM consumptions WHERE id = :id");
      $select->bindValue(":id", $id, PDO::PARAM_INT);
      $select->execute();

      return new Consumption(
            $id,
            $drinkId,
            $developerName,
            (string) $select->fetchColumn()
        );
    }

    public function caffeineCalculation(string $developerName): int{
        $req = $this->pdo->prepare("SELECT SUM(drinks.caffeine_mg) FROM consumptions JOIN drinks ON consumptions.drink_id = drinks.id WHERE developer_name = :developerName AND DATE(consumed_at) = CURDATE()");
        $req->bindValue(":developerName", $developerName, PDO::PARAM_STR);
        $req->execute();

        return (int) $req->fetchColumn();
    }
}