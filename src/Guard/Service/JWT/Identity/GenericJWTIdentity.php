<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Identity;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class GenericJWTIdentity implements Identity
{
    public function getIdentifier(): IdentifierValue
    {
        // TODO: Implement getIdentifier() method.
    }

    public function getRoles(): array
    {
        // TODO: Implement getRoles() method.
    }
}