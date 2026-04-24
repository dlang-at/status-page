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

    public function getByToken(string $token, bool $filterEnabled = true, bool $filterPublished = true): ?Check
    {
        /** @var ?Check $result */
        $result = $this->updownio->getAs('checks/' . urlencode($token), Check::class);

        if ($filterEnabled && !$result->enabled) {
            return null;
        }

        if ($filterPublished && !$result->published) {
            return null;
        }

        return $result;
    }
}
