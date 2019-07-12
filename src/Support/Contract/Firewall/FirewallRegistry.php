<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Firewall;

use Closure;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

interface FirewallRegistry
{
    /**
     * @param FirewallAware $firewall
     * @param Closure $make
     * @return FirewallAware|Closure
     */
    public function compose(FirewallAware $firewall, Closure $make);
}