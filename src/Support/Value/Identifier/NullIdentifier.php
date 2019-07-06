<?php

namespace MerchantOfComplexity\Authters\Support\Value\Identifier;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use function get_class;

final class NullIdentifier implements IdentifierValue
{
    /**
     * @throws RuntimeException
     */
    public function getValue()
    {
        throw new RuntimeException("Null identifier should never be called");
    }

    /**
     * @throws RuntimeException
     */
    public function identify()
    {
        throw new RuntimeException("Null identifier should never be called");
    }

    public function sameValueAs(Value $aValue): bool
    {
        return get_class($this) === get_class($aValue);
    }
}