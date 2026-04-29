<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Util;

use DomainException;

class IpAddressMasker
{
    public function __construct(
        private CidrMaskIPv4 $maskIPv4 = CidrMaskIPv4::Slash23,
        private CidrMaskIPv6 $maskIPv6 = CidrMaskIPv6::Slash48,
    ) {
    }

    public function mask(string $ipAddress, bool $appendCidrMaskToResult = false): ?string
    {
        $mask = (strpos($ipAddress, ':') === false)
            ? $this->maskIPv4
            : $this->maskIPv6;

        $ipAddressDecoded = inet_pton($ipAddress);
        if ($ipAddressDecoded === false) {
            return null;
        }

        $ipAddressMasked = inet_ntop($ipAddressDecoded & $mask->value);
        if ($ipAddressMasked === false) {
            throw new DomainException('Unsuitable IP address mask.');
        }

        if ($appendCidrMaskToResult) {
            return $ipAddressMasked . '/' . $mask->getPrefixBitCount();
        }

        return $ipAddressMasked;
    }
}
