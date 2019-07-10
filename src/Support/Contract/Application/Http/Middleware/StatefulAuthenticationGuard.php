<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;

interface StatefulAuthenticationGuard extends AuthenticationGuard
{
    public function setRecaller(Recallable $recaller): void;
}