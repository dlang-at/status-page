<?php

declare(strict_types=1);


namespace DlangAT\StatusPage\Util;

enum CidrMaskIPv4: string
{
    case Slash0 = "\x00\x00\x00\x00";

    case Slash1 = "\x80\x00\x00\x00";
    case Slash2 = "\xC0\x00\x00\x00";
    case Slash3 = "\xE0\x00\x00\x00";
    case Slash4 = "\xF0\x00\x00\x00";
    case Slash5 = "\xF8\x00\x00\x00";
    case Slash6 = "\xFC\x00\x00\x00";
    case Slash7 = "\xFE\x00\x00\x00";

    case Slash8 = "\xFF\x00\x00\x00";

    case Slash9 = "\xFF\x80\x00\x00";
    case Slash10 = "\xFF\xC0\x00\x00";
    case Slash11 = "\xFF\xE0\x00\x00";
    case Slash12 = "\xFF\xF0\x00\x00";
    case Slash13 = "\xFF\xF8\x00\x00";
    case Slash14 = "\xFF\xFC\x00\x00";
    case Slash15 = "\xFF\xFE\x00\x00";

    case Slash16 = "\xFF\xFF\x00\x00";

    case Slash17 = "\xFF\xFF\x80\x00";
    case Slash18 = "\xFF\xFF\xC0\x00";
    case Slash19 = "\xFF\xFF\xE0\x00";
    case Slash20 = "\xFF\xFF\xF0\x00";
    case Slash21 = "\xFF\xFF\xF8\x00";
    case Slash22 = "\xFF\xFF\xFC\x00";
    case Slash23 = "\xFF\xFF\xFE\x00";

    case Slash24 = "\xFF\xFF\xFF\x00";

    case Slash25 = "\xFF\xFF\xFF\x80";
    case Slash26 = "\xFF\xFF\xFF\xC0";
    case Slash27 = "\xFF\xFF\xFF\xE0";
    case Slash28 = "\xFF\xFF\xFF\xF0";
    case Slash29 = "\xFF\xFF\xFF\xF8";
    case Slash30 = "\xFF\xFF\xFF\xFC";
    case Slash31 = "\xFF\xFF\xFF\xFE";

    case Slash32 = "\xFF\xFF\xFF\xFF";

    public function getPrefixBitCount(): int
    {
        return (int)substr($this->name, 5);
    }

    public function getHostIdentifierBitCount(): int
    {
        return 32 - $this->getPrefixBitCount();
    }
}
