<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Model\DashboardViewMode;
use DlangAT\StatusPage\Model\UserSettings;
use DlangAT\StatusPage\Repository\CheckRepository;
use DlangAT\StatusPage\Repository\DashboardRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
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
        UserSettings $userSettings,
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

        $checksAlert = [];
        $checksUp = [];
        foreach ($checks as $check) {
            if ($check->isUpConfirmed()) {
                $checksUp[] = $check;
            } else {
                $checksAlert[] = $check;
            }
        }

        unset($checks);

        return $this->templateEngine->render($response, 'Pages/Dashboard.latte', [
            'checksAlert' => $checksAlert,
            'checksUp' => $checksUp,
            'dashboard' => $dashboard,
            'userSettings' => $userSettings,
        ]);
    }

    public function bySlugPost(
        Request $request,
        Response $response,
        string $slug,
        DashboardRepository $dashboardRepository,
        UserSettings $userSettings,
    ): Response {
        $found = $dashboardRepository->hasBySlug($slug);
        if (!$found) {
            throw new HttpNotFoundException($request);
        }

        $data = $request->getParsedBody();
        if (!is_array($data)) {
            throw new HttpBadRequestException($request)->setDescription('Unsupported request body.');
        }

        if (isset($data['dashboard-view-mode'])) {
            $dashboardViewMode = DashboardViewMode::tryFrom($data['dashboard-view-mode']);

            if ($dashboardViewMode === null) {
                throw new HttpBadRequestException($request, 'Bad `dashboard-view-mode`.');
            }

            $userSettings->setDashboardViewMode($dashboardViewMode);
        }

        return $response
            ->withStatus(303)
            ->withHeader('Location', '/dashboards/' . urlencode($slug));
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
