<?php

use App\Controllers\DrinkController;
use App\Core\Database;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\EcoMetricsMiddleware;

require_once "../vendor/autoload.php";


$dotenv = Dotenv::createImmutable("../");
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions([
  PDO::class => function(){
    return Database::getInstance();
  }
]);

$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();
$app->add(new EcoMetricsMiddleware());
$app->add(new CorsMiddleware);
$app->addBodyParsingMiddleware();

$app->options("/{routes:.+}", function ($request, $response){
  return $response;
});

$app->get("/api/drinks", [DrinkController::class, "index"]);
$app->post("/api/drinks", [DrinkController::class, "storeDrink"]);
$app->get("/api/drinks/{id:[0-9]+}", [DrinkController::class, "show"]);
$app->get("/api/consumptions", [ConsumptionController::class, "index"]);
$app->post("/api/consumptions", [ConsumptionController::class, "store"]);

$app->run();