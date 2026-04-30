<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Repository\CheckRepository;
use DlangAT\StatusPage\Repository\DashboardRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

final class DashboardsController extends ControllerBase
{
    public function index(
        Request $request,
        Response $response,
        DashboardRepository $dashboardRepository,
    ): Response {
        $firstDashboard = $dashboardRepository->getFirst();

        if ($firstDashboard === null) {
            throw new HttpNotFoundException($request);
        }

        return $response
            ->withStatus(301)
            ->withHeader('Location', $firstDashboard->getSubPageLink());
    }

    public function bySlug(
        Request $request,
        Response $response,
        string $slug,
        CheckRepository $checkRepository,
        DashboardRepository $dashboardRepository,
    ): Response {
        $dashboard = $dashboardRepository->getBySlug($slug);
        if ($dashboard === null) {
            throw new HttpNotFoundException($request);
        }

        $checks = [];
        foreach ($dashboard->checkTokens as $checkToken) {
            $check = $checkRepository->getByToken($checkToken);
            if ($check === null) {
                continue;
            }
            $checks[] = $check;
        }

        $downCount = 0;
        foreach ($checks as $check) {
            if (!$check->isUpConfirmed()) {
                ++$downCount;
            }
        }

        return $this->templateEngine->render($response, 'Pages/Dashboard.latte', [
            'checks' => $checks,
            'dashboard' => $dashboard,
            'downCount' => $downCount,
        ]);
    }

    public function bySlugRedirect(
        Request $request,
        Response $response,
        string $slug,
        DashboardRepository $dashboardRepository,
    ): Response {
        $found = $dashboardRepository->hasBySlug($slug);
        if (!$found) {
            throw new HttpNotFoundException($request);
        }

        return $response
            ->withStatus(301)
            ->withHeader('Location', '/dashboards/' . urlencode($slug));
    }
}
