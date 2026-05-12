<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Model\Cookie;

use DateTimeInterface;
use Psr\Http\Message\ResponseInterface as Response;

final class Cookie
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,

        public readonly ?string $domain = null,
        public readonly ?DateTimeInterface $expires = null,
        public readonly bool $httpOnly = false,
        public readonly ?int $maxAge = null,
        public readonly bool $partitioned = false,
        public readonly ?string $path = null,
        public readonly ?SameSite $sameSite = SameSite::Lax,
        public readonly bool $secure = false,
    ) {
    }

    public function appendToResponse($response): Response
    {
        return $response->withAddedHeader('Set-Cookie', $this->__toString());
    }

    public function __toString(): string
    {
        $name = urlencode($this->name);
        $value = urlencode($this->value);
        $result = "{$name}={$value}";

        if ($this->domain !== null) {
            $domain = urlencode($this->domain);
            $result .= "; domain={$domain}";
        }

        if ($this->expires !== null) {
            $expires = gmdate(DateTimeInterface::RFC7231, $this->expires->getTimestamp());
            $result .= "; expires={$expires}";
        }

        if ($this->httpOnly) {
            $result .= "; HttpOnly";
        }

        if ($this->maxAge !== null) {
            $result .= "; Max-Age={$this->maxAge}";
        }

        if ($this->partitioned) {
            $result .= "; Partitioned";
        }

        if ($this->path !== null) {
            $result .= "; Path={$this->path}";
        }

        if ($this->sameSite !== null) {
            $result .= "; SameSite={$this->sameSite->value}";
        }

        if ($this->secure) {
            $result .= "; Secure";
        }

        return $result;
    }
}
