<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

final class Dashboard
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public array $checkTokens,
        public bool $hideSummary,
    ) {
    }

    public function getSubPageLink(): string
    {
        return '/dashboards/' . urlencode($this->slug);
    }

    public static function map(string $slug, ?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        $checkTokens = [];
        if (is_array($data['c'])) {
            foreach ($data['c'] as $checkToken) {
                $checkTokens[] = (string)$checkToken;
            }
        }
        return new self(
            $slug,
            $data['title'],
            $data['description'],
            $checkTokens,
            (isset($data['hide_summary'])) ? filter_var($data['hide_summary'], FILTER_VALIDATE_BOOL) : false,
        );
    }
}
