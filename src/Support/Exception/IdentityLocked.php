<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class IdentityLocked extends InvalidStatusException
{
    public static function identifier(IdentifierValue $identifier): IdentityLocked
    {
        return new self("identity {$identifier} is locked");
    }
}