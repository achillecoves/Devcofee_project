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
            return $handler->handle($request);
        } catch (Throwable $exception) {

            $status = $exception instanceof HttpException ? $exception->getCode() : 500;

            $this->logger->error($exception->getMessage(), ["exception" => $exception]);

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