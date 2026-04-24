<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DlangAT\StatusPage\Controller\ChecksController;
use DlangAT\StatusPage\Controller\RootController;
use Slim\App;

final class Router
{
    public static function install(App $app)
    {
        $app->addRoutingMiddleware();

        $app->redirect('/', '/dashboard', 301);
        $app->get('/checks', [ChecksController::class, 'index']);
        $app->get('/checks/{token}', [ChecksController::class, 'byToken']);
        $app->get('/dashboard', [RootController::class, 'dashboard']);
        $app->get('/legal', [RootController::class, 'legal']);

        if ($_ENV['APP_ENV'] === 'dev') {
            $app->addErrorMiddleware(true, true, true);
        } else {
            $displayErrorDetails = (bool)($_ENV['ERROR_DISPLAY_DETAILS'] ?? false);
            $logErrors = (bool)($_ENV['ERROR_DO_LOG'] ?? false);
            $logErrorDetails = (bool)($_ENV['ERROR_DO_LOG_DETAILS'] ?? false);
            $app->addErrorMiddleware($displayErrorDetails, $logErrors, $logErrorDetails);
        }
    }
}
