<?php

namespace App\Controllers;

use App\Services\StationService;
use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use App\Core\UrlHelper;

class DrinkController{

    public function __construct(
        private Drink $drink
    ){}

    public function index(){}
}