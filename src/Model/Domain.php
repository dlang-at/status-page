<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\DateTimeFactory;

final class Domain
{
    public function __construct(
        public ?DateTimeImmutable $testedAt,
        public ?DateTimeImmutable $expiresAt,
        public ?int $remainingDays,
        public string $source,
    ) {
    }

    public static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new self(
            DateTimeFactory::makeOrNull($data['tested_at']),
            DateTimeFactory::makeOrNull($data['expires_at']),
            $data['remaining_days'],
            $data['source'],
        );
    }
}
