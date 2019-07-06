<?php

namespace MerchantOfComplexity\Authters\Support\Value\Identifier;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;

final class AnonymousIdentifier implements IdentifierValue
{
    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->identify() === $aValue->identify();
    }

    public function getValue()
    {
        return '.anon';
    }

    public function identify(): string
    {
        return $this->getValue();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}