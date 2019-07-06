<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

interface Identity
{
    public function getIdentifier(): IdentifierValue;

    public function getRoles(): array;
}