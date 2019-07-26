<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class IdentityExpired extends InvalidStatusException
{
    public static function identifier(IdentifierValue $identifier): IdentityExpired
    {
        return new self("identity {$identifier} has expired");
    }
}