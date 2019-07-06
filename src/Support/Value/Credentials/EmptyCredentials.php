<?php

namespace MerchantOfComplexity\Authters\Support\Value\Credentials;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;

final class EmptyCredentials implements ClearCredentials
{
    public function getValue()
    {
        throw new RuntimeException("Empty credentials should never be called");
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this && $this->getValue() === $aValue->getValue();
    }
}