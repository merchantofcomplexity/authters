<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;

interface ModelIdentifier extends Identity, \Serializable
{
    public function newIdentityModelInstance(): ?Identity;

    public function getIdentityModel(): string;
}