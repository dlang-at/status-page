<?php

declare(strict_types=1);

namespace DlangAT\StatusPage;

use DI\ContainerBuilder;
use DlangAT\StatusPage\Util\TemplateEngine;
use Dotenv\Dotenv;
use Latte\Engine as LatteEngine;
use Latte\Loaders\FileLoader as LatteFileLoader;
use Psr\Container\ContainerInterface as Container;

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
            DI::class => $this,

            Dotenv::class => $dotenv,

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
                    $tplTmpDir = $container->get('paths.tmp') . '/tpl';
                    mkdir($tplTmpDir, 0750, true);

                    $latte->setCacheDirectory('tpl');
                    $latte->setAutoRefresh(false);
                }

                return $latte;
            },

            'path.tmp' => $paths['tmp'],
            'path.var' => $paths['var'],

            TemplateEngine::class => function (Container $container, LatteEngine $engine) {
                return new TemplateEngine($engine, $container->get('format.datetime'));
            }
        ];
    }

    private function makeDotenv(): Dotenv
    {
        $dotenv = Dotenv::createImmutable($this->appRoot);
        $dotenv->load();

        $dotenv->required('APP_ENV')->allowedValues(['prod', 'dev']);
        $dotenv->ifPresent('DATETIME_FORMAT')->notEmpty();
        $dotenv->required('PAGE_LEGAL_LABEL')->notEmpty();
        $dotenv->required('PAGE_LEGAL_TEXT')->notEmpty();
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
            mkdir($diTmpPath, 0750, true);

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
