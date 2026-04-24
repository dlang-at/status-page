<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

final class Timings
{
    public function __construct(
        public int $redirect,
        public int $namelookup,
        public int $connection,
        public int $handshake,
        public int $response,
        public int $total,
    ) {
    }

    public static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new self(
            $data['redirect'] ?? 0,
            $data['namelookup'] ?? 0,
            $data['connection'] ?? 0,
            $data['handshake'] ?? 0,
            $data['response'] ?? 0,
            $data['total'] ?? 0,
        );
    }
}
