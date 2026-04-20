<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;

final class Downtime
{
    public function __construct(
        public string $id,
        public string $detailsUrl,
        public string $error,
        public DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $endedAt,
        public int $duration,
        bool $partial,
    ) {
    }
}
