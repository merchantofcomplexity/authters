<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

use MerchantOfComplexity\Authters\Guard\Service\Recaller\RecallerIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;

interface ReadRecallerProvider
{
    public function requireIdentityOfRecaller(RecallerIdentifier $identifier): Identity;
}