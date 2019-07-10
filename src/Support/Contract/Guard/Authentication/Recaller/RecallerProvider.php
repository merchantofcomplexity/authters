<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;

interface RecallerProvider
{
    public function getRecallerIdentifier(): ?RecallerIdentifier;

    public function requireIdentityOfRecaller(RecallerIdentifier $identifier): Identity;


    public function refreshIdentityRecaller(Identity $currentIdentity,
                                            RecallerIdentifier $newIdentifier): Identity;
}