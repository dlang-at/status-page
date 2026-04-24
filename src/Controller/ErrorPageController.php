<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Util\TemplateEngine;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Throwable;

final class ErrorPageController
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private TemplateEngine $templateEngine,
    ) {
    }

    public function notFound(Request $request, Throwable $exception, bool $displayErrorDetails): Response
    {
        if (!($exception instanceof HttpNotFoundException)) {
            throw new \ErrorException('Unsupported type of `Throwable`.');
        }

        $tpl = ($displayErrorDetails) ? [
            'exception' => $exception,
            'request' => $request,
        ] : [];

        $response = $this->responseFactory->createResponse(404, 'Not Found');
        return $this->templateEngine->render($response, 'Pages/Error/NotFound.latte', $tpl);
    }
}
