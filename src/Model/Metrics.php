<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

final class Metrics
{
    public function __construct(
        public float $uptime,
        public float $apdex,
        public Timings $timings,
        public Requests $requests,
    ) {
    }

    public static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new self(
            $data['uptime'],
            $data['apdex'],
            Timings::map($data['timings']),
            Requests::map($data['requests']),
        );
    }
}
