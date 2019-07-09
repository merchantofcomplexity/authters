<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Firewall;

use Closure;
use MerchantOfComplexity\Authters\Firewall\Builder;

interface FirewallRegistry
{
    public function compose(Builder $auth, Closure $make);
}