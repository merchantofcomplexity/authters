<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

interface StatefulAuthenticationMiddleware extends AuthenticationMiddleware
{
    public function setRecaller($recaller): void;
}