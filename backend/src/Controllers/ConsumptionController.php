<?php

namespace App\Controllers;

use App\Services\CoffeeService;
use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use App\Core\UrlHelper;

class ConsumptionController
{

    public function __construct(
        private CoffeeService $coffeeService
    ) {}

    public function index(Request $request, Response $response): Response
    {
        $consumptions = $this->coffeeService->getLastConsumptions();
        $baseUrl = UrlHelper::getBaseUrl($request);

        $data = array_map(fn($s) => [
            "id" => $s->id,
            "drinkId" => $s->drinkId,
            "developerName" => $s->developerName,
            "consumedAt" => $s->consumedAt,
            "_links" => [
                "self" => [
                    "href" => "{$baseUrl}/api/consumptions/{$s->id}",
                    "method" => "GET"
                ]
            ]
        ], $consumptions);

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $response->withHeader("Content-Type", "application/json")->withStatus(200);
    }

    public function store(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? json_decode($request->getBody()->getContents(), true);

        if (empty($body["drinkId"]) || empty($body["developerName"])) {
            return ProblemDetailsResponse::create(
                response: $response,
                status: 400,
                title: "Validation Error",
                detail: "Les champs drinkId et developerName sont requis."
            );
        }

        $drinkCaffeine = $this->coffeeService->getDrinkCaffeine((int) $body["drinkId"]);
        if ($drinkCaffeine === null) {
            return ProblemDetailsResponse::create(
                response: $response,
                status: 404,
                title: "Drink Not Found",
                detail: "La boisson avec l'id {$body["drinkId"]} n'existe pas."
            );
        }

        $alreadyConsumed = $this->coffeeService->caffeineCalculation($body["developerName"]);

        if ($alreadyConsumed + $drinkCaffeine > 400) {
            return ProblemDetailsResponse::create(
                response: $response,
                status: 429,
                title: "Caffeine Limit Exceeded",
                detail: "Seuil de caféine dépassé ! {$body["developerName"]} a déjà consommé {$alreadyConsumed}mg aujourd'hui (limite : 400mg)."
            );
        }

        $consumption = $this->coffeeService->createConsumption(
            (int) $body["drinkId"],
            $body["developerName"]
        );

        $baseUrl = UrlHelper::getBaseUrl($request);
        $data = [
            "id" => $consumption->id,
            "drinkId" => $consumption->drinkId,
            "developerName" => $consumption->developerName,
            "consumedAt" => $consumption->consumedAt,
            "_links" => [
                "delete" => [
                    "href" => "{$baseUrl}/api/consumptions/{$consumption->id}",
                    "method" => "GET"
                ]
            ]
        ];

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES));
        return $response->withHeader("Content-Type", "application/json")->withStatus(201);
    }

    public function deleteConsumption(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args["id"];
        $consumption = $this->coffeeService->getConsummptionById($id);

        if (empty($consumption)) {
            return ProblemDetailsResponse::create(
                response: $response,
                status: 404,
                title: "Consommation Not Found",
                detail: "La consommation à supprimer n'existe pas"
            );
        }

        $this->coffeeService->deleteConsumption($id);

        return $response->withStatus(204);
    }
}
