<?php

use App\Controllers\DrinkController;
use App\Controllers\ConsumptionController;
use App\Core\Database;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\EcoMetricsMiddleware;
use App\Middlewares\ErrorHandlingMiddleware;
use App\Responses\ProblemDetailsResponse;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

require_once "../vendor/autoload.php";


$dotenv = Dotenv::createImmutable("../");
$dotenv->load();

$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);

$containerBuilder->addDefinitions([
  PDO::class => function () {
    return Database::getInstance();
  },

  LoggerInterface::class => function () {
    $logger = new Logger("devcoffee");
    $logger->pushHandler(new StreamHandler(__DIR__ . "/../src/var/logs/app.log", Level::Debug));
    return $logger;
  }
]);

$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();

$logger = $container->get(LoggerInterface::class);

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

//Error Middleware
$app->add(new ErrorHandlingMiddleware($app->getResponseFactory(), $logger));

$app->add(new EcoMetricsMiddleware());
$app->add(new CorsMiddleware);

$app->options("/{routes:.+}", function ($request, $response) {
  return $response;
});

$app->get("/api/drinks", [DrinkController::class, "index"]);
$app->post("/api/drinks", [DrinkController::class, "storeDrink"]);
$app->get("/api/drinks/{id:[0-9]+}", [DrinkController::class, "showDrinks"]);
$app->get("/api/consumptions", [ConsumptionController::class, "index"]);
$app->post("/api/consumptions", [ConsumptionController::class, "store"]);
$app->delete("/api/consumptions/{id:[0-9]+}", [ConsumptionController::class, "deleteConsumption"]);

$app->run();
