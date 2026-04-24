<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

final class Requests
{
    public function __construct(
        public int $samples,
        public int $failures,
        public int $satisfied,
        public int $tolerated,
        public array $byResponseTime,
    ) {
    }

    public static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        $byResponseTime = [];
        if (isset($data['byResponseTime']) && is_array($data['byResponseTime'])) {
            foreach ($data['byResponseTime'] as $key => $value) {
                $byResponseTime[(string)$key] = (int)$value;
            }
        }

        return new self(
            $data['samples'],
            $data['failures'],
            $data['satisfied'],
            $data['tolerated'],
            $byResponseTime,
        );
    }
}
