<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\DateTimeFactory;

final class Check
{
    public function __construct(
        public string $token,
        public string $url,
        public string $type,
        public ?string $alias,
        public float $uptime,
        public bool $down,
        public ?DateTimeImmutable $downSince,
        public ?DateTimeImmutable $upSince,
        public ?string $error,
        public int $period,
        public float $apdexT,
        public ?string $stringMatch,
        public bool $enabled,
        public bool $published,
        public array $recipients,
        public ?DateTimeImmutable $lastCheckAt,
        public ?DateTimeImmutable $nextCheckAt,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $muteUntil,
        public ?array $customHeaders,
        public ?string $httpVerb,
        public ?string $httpBody,
        public ?Ssl $ssl,
        public ?Domain $domain,
    ) {
    }

    public function isDegraded(): bool
    {
        return (!$this->down && ($this->upSince === null));
    }

    public function isRecovering(): bool
    {
        return ($this->down && ($this->downSince === null));
    }

    public function isUpConfirmed(): bool
    {
        return (!$this->down && ($this->upSince !== null));
    }

    public function getSubPageLink(): string
    {
        return '/checks/' . urlencode($this->token);
    }

    public function getDowntimesSubPageLink(int $page = 1): string
    {
        return '/checks/' . urlencode($this->token) . '/downtimes/' . $page;
    }

    public function getTitle(): string
    {
        return $this->alias ?? $this->url;
    }

    static function map(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        $result = new self(
            $data['token'],
            $data['url'],
            $data['type'],
            (strlen($data['alias']) === 0) ? null : $data['alias'],
            $data['uptime'],
            $data['down'],
            DateTimeFactory::makeOrNull($data['down_since']),
            DateTimeFactory::makeOrNull($data['up_since']),
            $data['error'],
            $data['period'],
            $data['apdex_t'],
            $data['string_match'],
            $data['enabled'],
            $data['published'],
            $data['recipients'],
            DateTimeFactory::makeOrNull($data['last_check_at']),
            DateTimeFactory::makeOrNull($data['next_check_at']),
            DateTimeFactory::makeOrNull($data['created_at']),
            $data['mute_until'],
            $data['custom_headers'] ?? null,
            $data['http_verb'] ?? null,
            $data['http_body'] ?? null,
            Ssl::map($data['ssl']),
            (isset($data['domain'])) ? Domain::map($data['domain']) : null,
        );

        return $result;
    }
}
