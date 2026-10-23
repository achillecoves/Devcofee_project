<?php

namespace App\Middlewares;

use App\Responses\ProblemDetailsResponse;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Throwable;

class ErrorHandlingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger
    ) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        try {
            // Cas normal : on laisse la requête continuer son chemin.
            return $handler->handle($request);
        } catch (Throwable $exception) {
            // Cas d'erreur : on arrive ici si quelque chose a lancé une exception plus loin.

            // Une HttpException (404, 405...) porte son code HTTP, sinon c'est un 500.
            $status = $exception instanceof HttpException ? $exception->getCode() : 500;

            // On écrit l'erreur dans app.log.
            $this->logger->error($exception->getMessage(), ["exception" => $exception]);

            // Pour un 500, on cache le message technique au client.
            $detail = $status === 500
                ? "Une erreur interne est survenue."
                : $exception->getMessage();

            return ProblemDetailsResponse::create(
                response: $this->responseFactory->createResponse(),
                status: $status,
                title: $status === 500 ? "Internal Server Error" : "HTTP Error",
                detail: $detail
            );
        }
    }
}