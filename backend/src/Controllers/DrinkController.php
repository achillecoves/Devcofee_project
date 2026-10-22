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
                    "href" => "{$baseurl}/api/drinks/{$s->$id}",
                    "method" => "GET"
                ]
            ]
        ], $drinks);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));

        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }

    public function storeSession(Request $request, Response $response): Response{

      $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);

      if( empty($body["name"])  || empty($body["caffeineMg"]) || empty($body["volumeMl"])){

        return ProblemDetailsResponse::create(
          response: $response, 
          status: 400, 
          title: "Validation Error", 
          detail: "Les champs name, caffeineMg et volumeMl sont requis."
          );

      }

      $station = $this->stationService->getStationById((int) $body["stationId"]);

      if(!$station){
        return ProblemDetailsResponse::create(
          response: $response,
          status:404,
          title: "Station Not Found",
          detail: "La borne de recharge demandée n'existe pas."
        );
      }

      if((int) $body["kwhRequested"] > $station->powerKw){
        return ProblemDetailsResponse::create(
          response: $response,
          status: 429,
          title: "Power Exceeded",
          detail: "La puissance demandée ({$body['kwhRequested']} kWh) dépasse la limite de la borne ({$station->powerKw} kW)."
        );
      }

      $session = $this->stationService->createChargeSession(
        stationId: (int) $body["stationId"],
        userName: $body["userName"],
        kwhRequested: (int) $body["kwhRequested"]
      );

      $baseUrl = UrlHelper::getBaseUrl($request);

      $payload = [
        "id" => $session->id,
        "stationId" => $session->stationId,
        "userName" => $session->userName,
        "kwhRequested" => $session->kwhRequested,
        "createdAt" => $session->createdAt,
        "_links" => [
          "station" => [
            "href" => "{$baseUrl}/api/stations/{$session->stationId}",
            "method" => "GET"
            ]
        ]
      ];

      $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_SLASHES));

      return $response->withHeader("Content-Type", "application/json")->withStatus(201);

    }
}