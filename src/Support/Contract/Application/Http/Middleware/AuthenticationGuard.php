<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Guardable;

interface AuthenticationGuard extends Authentication
{
    public function setGuard(Guardable $guard): void;

    public function hasGuard(): bool;
}