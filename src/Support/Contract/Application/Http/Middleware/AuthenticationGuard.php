<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;

interface AuthenticationGuard extends Authentication
{
    public function setGuard(Guardable $guard): void;

    public function hasGuard(): bool;
}