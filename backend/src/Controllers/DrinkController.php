<?php

namespace App\Controllers;

use App\Services\CoffeeService;
use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use App\Core\UrlHelper;

class DrinkController{

    public function __construct(
        private CoffeeService $coffeeService
    ){}

    public function index(Request $request, Response $response): Response
    {
        $drinks = $this->coffeeService->getAllDrinks();

        $baseUrl = UrlHelper::getBaseUrl($request);

        $data = array_map(fn($s) => [
            "id" => $s->id,
            "name" => $s->name_drink,
            "caffeineMg" => $s->caffeine_mg,
            "volumeMl" => $s->volume_ml,
            "_links" => [
                "self" => [
                    "href" => "{$baseUrl}/api/drinks/{$s->id}",
                    "method" => "GET"
                ]
            ]
        ], $drinks);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));

        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }

    public function storeDrink(Request $request, Response $response): Response{

      $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);

      if( empty($body["name"])  || !isset($body["caffeineMg"]) || empty($body["volumeMl"])){

        return ProblemDetailsResponse::create(
          response: $response, 
          status: 400, 
          title: "Validation Error", 
          detail: "Les champs name, caffeineMg et volumeMl sont requis."
          );

      }

      $drink = $this->coffeeService->createDrink(
        name_drink: (string) $body["name"],
        caffeine_mg: (int) $body["caffeineMg"],
        volume_ml: (int) $body["volumeMl"]
      );

      $baseUrl = UrlHelper::getBaseUrl($request);

      $payload = [
        "id" => $drink->id,
        "name" => $drink->name_drink,
        "caffeineMg" => $drink->caffeine_mg,
        "volumeMl" => $drink->volume_ml,
        "_links" => [
                "self" => [
                    "href" => "{$baseUrl}/api/drinks/{$drink->id}",
                    "method" => "GET"
                ]
            ]
      ];

      $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

      return $response->withHeader("Content-Type", "application/json")->withStatus(201);

    }
}