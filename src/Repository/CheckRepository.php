<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Repository;

use DlangAT\StatusPage\Model\Check;
use DlangAT\StatusPage\Util\UpdownioApiClient;

final class CheckRepository
{
    public function __construct(
        private UpdownioApiClient $updownio,
    ) {
    }

    /**
     * @return Check[]
     */
    public function getAll(bool $filterEnabled = true, bool $filterPublished = true): array
    {
        $result = $this->updownio->getAsArrayOf('checks', Check::class);

        if ($filterEnabled) {
            $result = array_filter($result, function (Check $check) {
                return $check->enabled;
            });
        }

        if ($filterPublished) {
            $result = array_filter($result, function (Check $check) {
                return $check->published;
            });
        }

        return $result;
    }
}
