<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DomainException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class UpdownioApiClient
{
    private const ACCEPT_ENCODING_AUTO = '';

    private Client $client;

    public function __construct()
    {
        $timeout = $_ENV['UPDOWNIO_API_TIMEOUT'] ?? 5;

        $this->client = new Client([
            'base_uri' => 'https://updown.io/api/',
            'curl' => [
                CURLOPT_ACCEPT_ENCODING => self::ACCEPT_ENCODING_AUTO,
            ],
            'headers' => [
                'Accept' => 'application/json',
                'X-API-KEY' => $_ENV['UPDOWNIO_API_KEY'],
            ],
            'timeout' => $timeout,
        ]);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

    /**
     * @template T
     * @param class-string<T> $as
     * @return ?T
     * @throws GuzzleException
     */
    public function getAs(string $resource, string $as, ?array $query = null)
    {
        try {
            $raw = $this->get($resource, $query);
        } catch (GuzzleException $ex) {
            if ($ex->getCode() === 404) {
                return null;
            }
            throw $ex;
        }

        $json = json_decode($raw, true, JSON_THROW_ON_ERROR);
        return $this->map($json, $as);
    }

    /**
     * @throws GuzzleException
     */
    public function getAsArrayOf(string $resource, string $as, ?array $query = null): ?array
    {
        try {
            $raw = $this->get($resource, $query);
        } catch (GuzzleException $ex) {
            if ($ex->getCode() === 404) {
                return null;
            }
            throw $ex;
        }

        $json = json_decode($raw, true, JSON_THROW_ON_ERROR);
        return array_map(function ($item) use ($as) {
            return $this->map($item, $as);
        }, $json);
    }
}
