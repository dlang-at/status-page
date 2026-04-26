<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Repository;

use DlangAT\StatusPage\Model\Downtime;
use DlangAT\StatusPage\Util\UpdownioApiClient;

final class DowntimeRepository
{
    public function __construct(
        private UpdownioApiClient $updownio,
    ) {
    }

    /**
     * @return ?Downtime[]
     */
    public function getByCheck(string $checkToken, int $page = 1): ?array
    {
        return $this->updownio->getAsArrayOf('checks/' . urlencode($checkToken) . '/downtimes', Downtime::class, [
            'page' => $page,
        ]);
    }
}
