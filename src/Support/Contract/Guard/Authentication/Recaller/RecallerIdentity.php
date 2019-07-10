<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

use MerchantOfComplexity\Authters\Guard\Service\Recaller\RecallerIdentifier;

interface RecallerIdentity
{
    public function getRecallerIdentifier(): ?RecallerIdentifier;
}