<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\DateTimeFactory;

final class Ssl
{
    public function __construct(
        public ?DateTimeImmutable $testedAt,
        public ?DateTimeImmutable $expiresAt,
        public bool $valid,
        public ?string $error,
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
            $data['valid'],
            $data['error'],
        );
    }
}
