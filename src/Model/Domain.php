<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\DateTimeFactory;

final class Domain
{
    public function __construct(
        public ?DateTimeImmutable $testedAt,
        public ?DateTimeImmutable $createdAt,
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
            DateTimeFactory::makeOrNull($data['testedAt']),
            DateTimeFactory::makeOrNull($data['createdAt']),
            $data['remainingDays'],
            $data['source'],
        );
    }
}
