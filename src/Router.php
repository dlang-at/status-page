<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DlangAT\StatusPage\Controller\ChecksController;
use DlangAT\StatusPage\Controller\DashboardsController;
use DlangAT\StatusPage\Controller\ErrorPageController;
use DlangAT\StatusPage\Controller\RootController;
use DlangAT\StatusPage\Middleware\DefaultHeaderMiddleware;
use DlangAT\StatusPage\Middleware\TelemetryMiddleware;
use Psr\Container\ContainerInterface as Container;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

final class Router
{
    public function __construct(
        private App $app,
        private Container $container,
        private TelemetryMiddleware $telemetryMiddleware,
    ) {
    }

    public function install(): void
    {
        $app = $this->app;
        $app->addRoutingMiddleware();

        if ($this->container->get('isProd')) {
            $app->addMiddleware(new DefaultHeaderMiddleware('Cache-Control', 'max-age=60'));
        }

        $app->get('/', [RootController::class, 'index']);
        $app->get('/checks', [ChecksController::class, 'index']);
        $app->redirect('/checks/', '/checks', 301);
        $app->get('/checks/{token}', [ChecksController::class, 'byToken']);
        $app->get('/checks/{token}/', [ChecksController::class, 'byTokenRedirect']);
        $app->get('/checks/{token}/downtimes', [ChecksController::class, 'byTokenDowntimesRedirect']);
        $app->get('/checks/{token}/downtimes/', [ChecksController::class, 'byTokenDowntimesRedirect']);
        $app->get('/checks/{token}/downtimes/{page}', [ChecksController::class, 'byTokenDowntimesByPage']);
        $app->get('/checks/{token}/downtimes/{page}/', [ChecksController::class, 'byTokenDowntimesByPageRedirect']);
        $app->get('/dashboards/{slug}', [DashboardsController::class, 'bySlug']);
        $app->get('/dashboards/{slug}/', [DashboardsController::class, 'bySlugRedirect']);
        $app->get('/legal', [RootController::class, 'legal']);
        $app->redirect('/legal/', '/legal', 301);

        if ($_ENV['APP_ENV'] === 'dev') {
            $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        } else {
            $displayErrorDetails = $this->container->get('error.display_details');
            $logErrors = $this->container->get('error.log.enabled');
            $logErrorDetails = $this->container->get('error.log.details');
            $errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
        }

        $errorMiddleware->setErrorHandler(HttpNotFoundException::class, [ErrorPageController::class, 'notFound']);

        if ($app->getContainer()->get('telemetry.enabled')) {
            $app->addMiddleware($this->telemetryMiddleware);
        }
    }
}
