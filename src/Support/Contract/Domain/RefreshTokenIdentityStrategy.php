<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface RefreshTokenIdentityStrategy
{
    public function refreshTokenIdentity(Tokenable $token): ?Tokenable;
}