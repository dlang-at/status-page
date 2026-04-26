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
    private const UPDOWN_API_PAGE_LIMIT = 999;
    private const UPDOWN_API_PAGE_SIZE = 100;

    public function byToken(
        Request $request,
        Response $response,
        string $token,
        CheckRepository $checkRepository,
        DowntimeRepository $downtimeRepository,
        MetricsRepository $metricsRepository,
    ): Response {
        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        $downtimesAll = $downtimeRepository->getByCheck($token);
        $downtimesCountAll = count($downtimesAll);
        $downtimes = array_slice($downtimesAll, 0, 10);
        $metrics = $metricsRepository->getByCheck($token);

        return $this->templateEngine->render($response, 'Pages/CheckDetails.latte', [
            'check' => $check,
            'downtimes' => $downtimes,
            'downtimesAreCutOff' => ($downtimesCountAll > 10),
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
            ->withHeader('Location', '/checks/' . urlencode($token) . '/downtimes/1');
    }

    public function byTokenDowntimesByPage(
        Request $request,
        Response $response,
        string $token,
        string $page,
        CheckRepository $checkRepository,
        DowntimeRepository $downtimeRepository,
    ): Response {
        $pageNum = filter_var($page, FILTER_VALIDATE_INT);
        if (($pageNum === false) || ($pageNum > self::UPDOWN_API_PAGE_LIMIT)) {
            throw new HttpNotFoundException($request);
        }

        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        $downtimes = $downtimeRepository->getByCheck($token, $pageNum);

        return $this->templateEngine->render($response, 'Pages/CheckDowntimes.latte', [
            'check' => $check,
            'downtimes' => $downtimes,
            'page' => $pageNum,
            'pageLimit' => self::UPDOWN_API_PAGE_LIMIT,
            'pageSize' => self::UPDOWN_API_PAGE_SIZE,
        ]);
    }

    public function byTokenDowntimesByPageRedirect(
        Request $request,
        Response $response,
        string $token,
        string $page,
        CheckRepository $checkRepository,
    ): Response {
        $pageNum = filter_var($page, FILTER_VALIDATE_INT);
        if (($pageNum === false) || ($pageNum > self::UPDOWN_API_PAGE_LIMIT)) {
            throw new HttpNotFoundException($request);
        }

        $check = $checkRepository->getByToken($token);
        if ($check === null) {
            throw new HttpNotFoundException($request);
        }

        return $response
            ->withStatus(301)
            ->withHeader('Location', '/checks/' . urlencode($token) . '/downtimes/' . urlencode($page));
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
