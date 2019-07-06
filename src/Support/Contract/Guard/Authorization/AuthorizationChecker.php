<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface AuthorizationChecker
{
    public function isGranted(Tokenable $token, array $attributes, object $subject = null): bool;
}