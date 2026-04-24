<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Repository;

use DateTimeInterface;
use DlangAT\StatusPage\Model\Metrics;
use DlangAT\StatusPage\Util\UpdownioApiClient;

final class MetricsRepository
{
    public function __construct(
        private UpdownioApiClient $updownio,
    ) {
    }

    public function getByCheck(string $checkToken): ?Metrics
    {
        return $this->updownio->getAs('checks/' . urlencode($checkToken) . '/metrics', Metrics::class);
    }

    public function getByCheckAggregated(string $checkToken, DateTimeInterface $from, DateTimeInterface $to): ?Metrics
    {
        return $this->updownio->getAs('checks/' . urlencode($checkToken) . '/metrics', Metrics::class, [
            'from' => $from->format(DateTimeInterface::ATOM),
            'to' => $to->format(DateTimeInterface::ATOM),
        ]);
    }
}
