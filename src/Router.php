<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DlangAT\StatusPage\Controller\ChecksController;
use DlangAT\StatusPage\Controller\DashboardsController;
use DlangAT\StatusPage\Controller\ErrorPageController;
use DlangAT\StatusPage\Controller\RootController;
use DlangAT\StatusPage\Middleware\DefaultHeaderMiddleware;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

final class Router
{
    public static function install(App $app)
    {
        $app->addRoutingMiddleware();

        if ($_ENV['APP_ENV'] === 'prod') {
            $app->addMiddleware(new DefaultHeaderMiddleware('Cache-Control', 'max-age=60'));
        }

        $app->redirect('/', '/dashboard', 301);
        $app->get('/checks', [ChecksController::class, 'index']);
        $app->redirect('/checks/', '/checks', 301);
        $app->get('/checks/{token}', [ChecksController::class, 'byToken']);
        $app->get('/checks/{token}/', [ChecksController::class, 'byTokenRedirect']);
        $app->get('/checks/{token}/downtimes', [ChecksController::class, 'byTokenDowntimesRedirect']);
        $app->get('/checks/{token}/downtimes/', [ChecksController::class, 'byTokenDowntimesRedirect']);
        $app->get('/checks/{token}/downtimes/{page}', [ChecksController::class, 'byTokenDowntimesByPage']);
        $app->get('/checks/{token}/downtimes/{page}/', [ChecksController::class, 'byTokenDowntimesByPageRedirect']);
        $app->get('/dashboard', [RootController::class, 'dashboard']);
        $app->redirect('/dashboard/', '/dashboard', 301);
        $app->get('/dashboards/{slug}', [DashboardsController::class, 'bySlug']);
        $app->get('/dashboards/{slug}/', [DashboardsController::class, 'bySlugRedirect']);
        $app->get('/legal', [RootController::class, 'legal']);
        $app->redirect('/legal/', '/legal', 301);

        if ($_ENV['APP_ENV'] === 'dev') {
            $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        } else {
            $displayErrorDetails = (bool)($_ENV['ERROR_DISPLAY_DETAILS'] ?? false);
            $logErrors = (bool)($_ENV['ERROR_DO_LOG'] ?? false);
            $logErrorDetails = (bool)($_ENV['ERROR_DO_LOG_DETAILS'] ?? false);
            $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
        }

        $errorMiddleware->setErrorHandler(HttpNotFoundException::class, [ErrorPageController::class, 'notFound']);
    }
}
