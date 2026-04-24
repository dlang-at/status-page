<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Repository\CheckRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class ChecksController extends ControllerBase
{
    public function byToken(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            return $response->withStatus(404);
        }

        return $this->templateEngine->render($response, 'Pages/CheckDetails.latte', [
            'check' => $check,
        ]);
    }

    public function index(
        Request $request,
        Response $response,
        CheckRepository $checkRepository,
    ): Response {
        return $this->templateEngine->render($response, 'Pages/Checks.latte', [
            'checks' => $checkRepository->getAll(),
        ]);
    }
}
