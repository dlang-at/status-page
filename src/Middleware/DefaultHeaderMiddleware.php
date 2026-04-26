<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class DefaultHeaderMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $headerName,
        private string $defaultValue,
    )
    {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        if ($response->hasHeader($this->headerName)) {
            return $response;
        }

        return $response->withHeader($this->headerName, $this->defaultValue);
    }
}
