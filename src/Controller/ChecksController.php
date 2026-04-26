<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Repository\CheckRepository;
use DlangAT\StatusPage\Repository\DowntimeRepository;
use DlangAT\StatusPage\Repository\MetricsRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

final class ChecksController extends ControllerBase
{
    public function byToken(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
        MetricsRepository $metricsRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        $metrics = $metricsRepository->getByCheck($token);

        return $this->templateEngine->render($response, 'Pages/CheckDetails.latte', [
            'check' => $check,
            'metrics' => $metrics,
        ]);
    }

    public function byTokenRedirect(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        return $response
            ->withStatus(301)
            ->withHeader('Location', '/checks/' . urlencode($token));
    }

    public function byTokenDowntimes(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
        DowntimeRepository $downtimeRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        $downtimes = $downtimeRepository->getByCheck($token);

        return $this->templateEngine->render($response, 'Pages/CheckDowntimes.latte', [
            'check' => $check,
            'downtimes' => $downtimes,
        ]);
    }

    public function byTokenDowntimesRedirect(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        return $response
            ->withStatus(301)
            ->withHeader('Location', '/checks/' . urlencode($token) . '/downtimes');
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
