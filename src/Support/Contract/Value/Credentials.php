<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Value;

interface Credentials extends Value
{
    /**
     * @return mixed
     */
    public function getValue();
}