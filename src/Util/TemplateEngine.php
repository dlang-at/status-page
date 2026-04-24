<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DateTimeInterface;
use Latte\Engine;
use Psr\Http\Message\ResponseInterface as Response;

final class TemplateEngine
{
    public function __construct(
        private Engine $latte,
        private string $dateTimeFormat,
    ) {
        $this->setupFilters();
    }

    private function setupFilters()
    {
        $this->latte->addFilter(
            'formatDateTime',
            function (DateTimeInterface $dateTime): string {
                return $dateTime->format($this->dateTimeFormat);
            }
        );

        $this->latte->addFilter('formatUptime', function (float $uptime): string {
            return number_format($uptime, 2, '.') . ' %';
        });
    }

    public function render(Response $response, string $template, array $params = []): Response
    {
        $response = $response->withHeader('Content-Type', 'text/html');
        $response->getBody()->write(
            $this->latte->renderToString($template, $params)
        );

        return $response;
    }
}
