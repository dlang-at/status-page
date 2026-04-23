<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Repository\CheckRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class RootController extends ControllerBase
{
    public function dashboard(
        Request $request,
        Response $response,
        CheckRepository $checkRepository,
    ): Response {
        $checks = $checkRepository->getAll();

        $downCount = 0;
        foreach ($checks as $check) {
            if (!$check->isUpConfirmed()) {
                ++$downCount;
            }
        }

        return $this->templateEngine->render($response, 'Pages/Dashboard.latte', [
            'checks' => $checks,
            'downCount' => $downCount,
        ]);
    }

    public function legal(Request $request, Response $response): Response
    {
        return $this->templateEngine->render($response, 'Pages/Legal.latte', [
            'legalPageText' => $_ENV['PAGE_LEGAL_TEXT'],
        ]);
    }
}
