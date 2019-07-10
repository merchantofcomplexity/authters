<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

interface RecallerIdentity
{
    public function getRecallerIdentifier(): ?RecallerIdentifier;
}