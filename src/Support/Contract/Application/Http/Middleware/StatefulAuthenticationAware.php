<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

interface StatefulAuthenticationAware extends AuthenticationAware
{
    public function setRecaller($recaller): void;
}