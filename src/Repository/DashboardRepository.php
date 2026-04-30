<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Repository;

use DlangAT\StatusPage\Model\Dashboard;
use Exception;

final class DashboardRepository
{
    private ?array $data = null;
    private ?array $mapped = null;

    public function __construct(
        private string $dashboardsFilePath,
    ) {
    }

    private function getData(): array
    {
        if ($this->data === null) {
            if (!file_exists($this->dashboardsFilePath)) {
                $this->data = [];
                return $this->data;
            }

            $iniData = parse_ini_file($this->dashboardsFilePath, true, INI_SCANNER_RAW);
            if ($iniData === false) {
                throw new Exception('Reading/parsing `dashboards.ini` failed.');
            }
            $this->data = $iniData;
        }

        return $this->data;
    }


    private function getMapped(): array
    {
        if ($this->mapped === null) {
            $this->mapped = [];
            foreach ($this->getData() as $slug => $dashboardData) {
                $this->mapped[$slug] = Dashboard::map($slug, $dashboardData);
            }
        }

        return $this->mapped;
    }

    /**
     * @return Dashboard[]
     */
    public function getAll(): array
    {
        return $this->getMapped();
    }

    public function getFirst(): ?Dashboard
    {
        $mapped = $this->getMapped();
        $first = reset($mapped);

        if ($first === false) {
            return null;
        }

        return $first;
    }

    public function hasBySlug(string $slug): bool
    {
        return array_key_exists($slug, $this->getData());
    }

    public function getBySlug(string $slug): ?Dashboard
    {
        return $this->getMapped()[$slug] ?? null;
    }
}
