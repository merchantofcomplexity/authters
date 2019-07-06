<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Value;

interface Value
{
    public function sameValueAs(Value $aValue): bool;

    /**
     * @return mixed
     */
    public function getValue();
}