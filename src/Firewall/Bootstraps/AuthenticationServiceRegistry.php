<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class AuthenticationServiceRegistry implements FirewallRegistry
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function compose(FirewallAware $firewall, Closure $make)
    {
        return $make($firewall);
    }
}