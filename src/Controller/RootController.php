<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;

final class RootController extends ControllerBase
{
    public function index(
        Request $request,
        Response $response,
        \DI\Container $container,
    ): Response {
        return $container->call([DashboardsController::class, 'index'], [$request, $response]);
    }

    public function legal(Request $request, Response $response, Container $container): Response
    {
        if ($container->get('isProd')) {
            $response = $response->withHeader('Cache-Control', 'max-age=86400');
        }

        $legalPageContentFile = $container->get('path.var') . '/legal.html';
        $legalPageContent = file_get_contents($legalPageContentFile);

        if ($legalPageContent === false) {
            throw new HttpInternalServerErrorException($request, 'Could not load `legal.html`.');
        }

        return $this->templateEngine->render($response, 'Pages/Legal.latte', [
            'legalPageContent' => $legalPageContent,
        ]);
    }
}
