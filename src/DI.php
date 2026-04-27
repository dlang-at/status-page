<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DI\ContainerBuilder;
use DlangAT\StatusPage\Middleware\TelemetryMiddleware;
use DlangAT\StatusPage\Repository\DashboardRepository;
use DlangAT\StatusPage\Util\IpAddressMasker;
use DlangAT\StatusPage\Util\TemplateEngine;
use Dotenv\Dotenv;
use GuzzleHttp\Psr7\HttpFactory;
use Latte\Engine as LatteEngine;
use Latte\Loaders\FileLoader as LatteFileLoader;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseFactoryInterface;

final class DI
{
    private ?Dotenv $dotenv = null;

    public function __construct(
        private string $appRoot,
    ) {
    }

    private function makeDefinitions(Dotenv $dotenv, array $paths): array
    {
        return [
            DashboardRepository::class => function (Container $container) {
                return new DashboardRepository($container->get('path.var') . '/dashboards.ini');
            },

            DI::class => $this,

            Dotenv::class => $dotenv,

            'error.display_details' =>  function () {
                if (!isset($_ENV['ERROR_DISPLAY_DETAILS'])) {
                    return false;
                }

                return filter_var($_ENV['ERROR_DISPLAY_DETAILS'], FILTER_VALIDATE_BOOL);
            },

            'error.log.enabled' =>  function () {
                if (!isset($_ENV['ERROR_DO_LOG'])) {
                    return false;
                }
                return filter_var($_ENV['ERROR_DO_LOG'], FILTER_VALIDATE_BOOL);
            },

            'error.log.details' =>  function () {
                if (!isset($_ENV['ERROR_DO_LOG_DETAILS'])) {
                    return false;
                }
                return filter_var($_ENV['ERROR_DO_LOG_DETAILS'], FILTER_VALIDATE_BOOL);
            },

            'format.datetime' => \DI\env('DATETIME_FORMAT'),

            LatteFileLoader::class => function () {
                return new LatteFileLoader(__DIR__ . '/View');
            },

            'isProd' => $this->isProd(),

            'keys.api.updownio' => \DI\env('UPDOWNIO_API_KEY'),

            LatteEngine::class => function (Container $container, LatteFileLoader $loader) {
                $latte = new LatteEngine();
                $latte->setLoader($loader);

                $isProd = $container->get('isProd');

                if ($isProd) {
                    $tplTmpDir = $container->get('path.tmp') . '/tpl';

                    if (!file_exists($tplTmpDir)) {
                        @mkdir($tplTmpDir, 0750, true);
                    }

                    $latte->setCacheDirectory($tplTmpDir);
                    $latte->setAutoRefresh(false);
                }

                return $latte;
            },

            'path.tmp' => $paths['tmp'],
            'path.var' => $paths['var'],

            ResponseFactoryInterface::class => \DI\get(HttpFactory::class),

            'telemetry.enabled' => function () {
                if (!isset($_ENV['TELEMETRY_ENABLED'])) {
                    return false;
                }
                return filter_var($_ENV['TELEMETRY_ENABLED'], FILTER_VALIDATE_BOOL);
            },

            TelemetryMiddleware::class => function (Container $container, IpAddressMasker $ipAddressMasker) {
                $logFilePath = $container->get('path.var') . '/telemetry.log';
                return new TelemetryMiddleware($ipAddressMasker, $logFilePath);
            },

            TemplateEngine::class => function (Container $container, LatteEngine $engine) {
                return new TemplateEngine($container, $engine, $container->get('format.datetime'));
            }
        ];
    }

    private function makeDotenv(): Dotenv
    {
        $dotenv = Dotenv::createImmutable($this->appRoot);
        $dotenv->load();

        $dotenv->required('APP_ENV')->allowedValues(['prod', 'dev']);
        $dotenv->required('APP_TITLE')->notEmpty();
        $dotenv->ifPresent('DATETIME_FORMAT')->notEmpty();
        $dotenv->required('PAGE_LEGAL_LABEL')->notEmpty();
        $dotenv->required('PAGE_LEGAL_TEXT')->notEmpty();
        $dotenv->ifPresent('TELEMETRY_ENABLED')->isBoolean();
        $dotenv->required('UPDOWNIO_API_KEY')->notEmpty();
        $dotenv->ifPresent('UPDOWNIO_API_TIMEOUT')->isInteger();

        $dotenv->ifPresent([
            'ERROR_DISPLAY_DETAILS',
            'ERROR_DO_LOG',
            'ERROR_DO_LOG_DETAILS',
        ])->isBoolean();

        return $dotenv;
    }

    private function getDotenv(): Dotenv
    {
        if ($this->dotenv === null) {
            $this->dotenv = $this->makeDotenv();
        }

        return $this->dotenv;
    }

    private function isProd(): bool
    {
        return ($_ENV['APP_ENV'] === 'prod');
    }

    public function makeContainer(): Container
    {
        $dotenv = $this->getDotenv();

        $builder = new ContainerBuilder();
        $builder->useAttributes(false);
        $builder->useAutowiring(true);

        $varPath = $this->appRoot . '/var';
        $tmpPath = $varPath . '/tmp';

        if ($this->isProd()) {
            $diTmpPath = $tmpPath . '/di';

            if (!file_exists($diTmpPath)) {
                @mkdir($diTmpPath, 0750, true);
            }

            $proxies = $diTmpPath . '/proxies';

            $builder->enableCompilation($tmpPath);
            $builder->writeProxiesToFile(true, $proxies);
        }

        $paths = [
            'tmp' => $tmpPath,
            'var' => $varPath,
        ];

        $builder->addDefinitions(
            $this->makeDefinitions($dotenv, $paths),
        );

        return $builder->build();
    }
}
