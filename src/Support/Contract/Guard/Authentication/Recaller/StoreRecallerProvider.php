<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

use MerchantOfComplexity\Authters\Guard\Service\Recaller\RecallerIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;

interface StoreRecallerProvider
{
    public function refreshIdentityRecaller(Identity $currentIdentity,
                                            RecallerIdentifier $newIdentifier): Identity;
}