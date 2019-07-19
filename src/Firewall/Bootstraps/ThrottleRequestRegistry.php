<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ThrottleRequestPreAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class ThrottleRequestRegistry implements FirewallRegistry
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
        $firewall->addPreService('throttle-request', $this->createService());

        return $make($firewall);
    }

    protected function createService(): callable
    {
        return function (Application $app, FirewallContext $context): Authentication {
            return new ThrottleRequestPreAuthentication(
                $context->contextKey(),
                $app->make(RateLimiter::class),
                $app->make(TokenStorage::class)
            );
        };
    }
}