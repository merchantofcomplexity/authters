<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class IdentityNotEnabled extends InvalidStatusException
{
    public static function identifier(IdentifierValue $identifier): IdentityNotEnabled
    {
        return new self("identity {$identifier} not enabled");
    }
}