<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface AuthorizationStrategy
{
    public function decide(Tokenable $token, array $attributes, object $subject = null): bool;
}