<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DomainException;
use GuzzleHttp\Client;

class UpdownioApiClient
{
    private Client $client;

    public function __construct()
    {
        $timeout = $_ENV['UPDOWNIO_API_TIMEOUT'] ?? 5;

        $this->client = new Client([
            'base_uri' => 'https://updown.io/api/',
            'headers' => [
                'Accept' => 'application/json',
                'X-API-KEY' => $_ENV['UPDOWNIO_API_KEY'],
            ],
            'timeout' => $timeout,
        ]);
    }

    public function get(string $resource, ?array $query = null): string
    {
        $response = $this->client->get($resource, ['query' => $query]);
        return $response->getBody()->getContents();
    }

    private function map(array $item, string $targetType)
    {
        if (!class_exists($targetType)) {
            throw new DomainException('Target type `' . $targetType . '` does not refer to an existing class.');
        }

        $mapper = [$targetType, 'map'];
        return $mapper($item);
    }

    public function getAs(string $resource, string $as, ?array $query = null): object
    {
        $raw = $this->get($resource, $query);
        $json = json_decode($raw, true, JSON_THROW_ON_ERROR);
        return $this->map($json, $as);
    }

    public function getAsArrayOf(string $resource, string $as, ?array $query = null): array
    {
        $raw = $this->get($resource, $query);
        $json = json_decode($raw, true, JSON_THROW_ON_ERROR);
        return array_map(function ($item) use ($as) {
            return $this->map($item, $as);
        }, $json);
    }
}
