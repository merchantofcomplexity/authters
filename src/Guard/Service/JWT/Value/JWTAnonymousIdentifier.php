<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Value;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;

final class JWTAnonymousIdentifier implements IdentifierValue
{
    public function identify(): string
    {
        return 'jwt_anonymous';
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->identify() === $aValue->identify();
    }

    public function getValue(): string
    {
        return $this->identify();
    }
}