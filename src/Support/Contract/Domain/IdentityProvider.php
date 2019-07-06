<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

interface IdentityProvider
{
    public function requireIdentityOfIdentifier(IdentifierValue $identifier): Identity;

    public function supportsIdentity(Identity $identity): bool;
}