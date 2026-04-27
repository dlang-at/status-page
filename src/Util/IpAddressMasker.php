<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DomainException;

class IpAddressMasker
{
    public function __construct(
        private string $maskIPv4 = "\xFF\xFF\x80\x00",
        private string $maskIPv6 = "\xFF\xFF\xFF\xFF" . "\xFF\xFF\x00\x00" . "\x00\x00\x00\x00" . "\x00\x00\x00\x00",
    ) {
    }

    public function mask(string $ipAddress): ?string
    {
        $mask = (strpos($ipAddress, ':') === false)
            ? $this->maskIPv4
            : $this->maskIPv6;

        $ipAddressDecoded = inet_pton($ipAddress);
        if ($ipAddressDecoded === false) {
            return null;
        }

        $ipAddressMasked = inet_ntop($ipAddressDecoded & $mask);
        if ($ipAddressMasked === false) {
            throw new DomainException('Unsuitable IP address mask.');
        }

        return $ipAddressMasked;
    }
}
