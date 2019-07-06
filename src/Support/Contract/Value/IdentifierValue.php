<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Value;

interface IdentifierValue extends Value
{
    /**
     * @return mixed
     */
    public function identify();

    // checkMe implement __toString ?
}