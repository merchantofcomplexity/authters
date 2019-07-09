<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;

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

    public function compose(Builder $auth, Closure $make)
    {
        foreach ($auth->services() as $authenticationService) {
            $auth->addRegistry($authenticationService);
        }

        return $make($auth);
    }
}