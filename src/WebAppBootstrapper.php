<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DI\Bridge\Slim\Bridge;

final class WebAppBootstrapper
{
    public static function run(string $appRoot): void
    {
        date_default_timezone_set('UTC');

        $di = new DI($appRoot);
        $container = $di->makeContainer();
        $app = Bridge::create($container);
        Router::install($app);
        $app->run();
    }
}
