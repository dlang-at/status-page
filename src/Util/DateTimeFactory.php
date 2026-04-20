<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DateTimeImmutable;

final class DateTimeFactory
{
    public static function makeOrNull(?string $date): ?DateTimeImmutable
    {
        if ($date === null) {
            return null;
        }

        return new DateTimeImmutable($date);
    }
}
