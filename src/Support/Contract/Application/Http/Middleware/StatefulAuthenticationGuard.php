<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

interface StatefulAuthenticationGuard extends AuthenticationGuard
{
    public function setRecaller($recaller): void;
}