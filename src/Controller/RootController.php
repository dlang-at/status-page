<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RootController extends ControllerBase
{
    public function index(
        Response $response,
        \DI\Container $container,
    ): Response {
        return $container->call([DashboardsController::class, 'index'], [$response]);
    }

    public function legal(Request $request, Response $response, Container $container): Response
    {
        if ($container->get('isProd')) {
            $response = $response->withHeader('Cache-Control', 'max-age=86400');
        }

        return $this->templateEngine->render($response, 'Pages/Legal.latte', [
            'legalPageText' => $_ENV['PAGE_LEGAL_TEXT'],
        ]);
    }
}
