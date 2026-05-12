<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DlangAT\StatusPage\Model\Cookie\Cookie;
use DlangAT\StatusPage\Model\Cookie\SameSite;

final class UserSettings
{
    private const COOKIE_PREFIX = 'dlang-status-page_user_settings__';
    private const SECONDS_ONE_YEAR = (86400 * 365);

    private bool $dirty = false;

    private DashboardViewMode $dashboardViewMode = DashboardViewMode::Grid;

    public function getDashboardViewMode(): DashboardViewMode
    {
        return $this->dashboardViewMode;
    }

    public function setDashboardViewMode(DashboardViewMode $dashboardViewMode): void
    {
        $this->dirty = true;
        $this->dashboardViewMode = $dashboardViewMode;
    }

    public function isDirty(): bool
    {
        return $this->dirty;
    }

    public function applyFromCookies(array $cookies): void
    {
        $nameDashboardViewMode = self::COOKIE_PREFIX . 'dashboard-view-mode';
        if (isset($cookies[$nameDashboardViewMode])) {
            $value = DashboardViewMode::tryFrom($cookies[$nameDashboardViewMode]);
            if ($value !== null) {
                $this->dashboardViewMode = $value;
            }
        }
    }

    public function toCookies(): array
    {
        return [
            new Cookie(
                name: self::COOKIE_PREFIX . 'dashboard-view-mode',
                value: $this->dashboardViewMode->value,
                maxAge: self::SECONDS_ONE_YEAR,
                path: '/',
                sameSite: SameSite::Lax,
            ),
        ];
    }
}
