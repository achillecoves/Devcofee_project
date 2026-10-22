<?php
namespace App\Services;

use App\Models\Drink;
use App\Models\Consumption;
use PDO;

class CoffeeService{

    public function __construct(
        private PDO $pdo
    ){}

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