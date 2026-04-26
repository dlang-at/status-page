<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\DateTimeFactory;

final class Downtime
{
    public function __construct(
        public string $id,
        public string $detailsUrl,
        public string $error,
        public DateTimeImmutable $startedAt,
        public ?DateTimeImmutable $endedAt,
        public int $duration,
        public bool $partial,
    ) {
    }

    public static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new self(
            $data['id'],
            $data['details_url'],
            $data['error'],
            DateTimeFactory::makeOrNull($data['started_at']),
            DateTimeFactory::makeOrNull($data['ended_at']),
            $data['duration'],
            $data['partial'],
        );
    }
}
