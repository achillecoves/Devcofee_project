<?php

use App\Controllers\DrinkController;
use App\Core\Database;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

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
$app->addBodyParsingMiddleware();

$app->options("/{routes:.+}", function ($request, $response){
  return $response;
});

$app->get("/api/drinks", [DrinkController::class, "index"]);
$app->post("/api/drinks", [DrinkController::class, "storeDrink"]);

$app->run();