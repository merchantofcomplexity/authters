<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\AnonymousAuthentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Providers\ProvideAnonymousAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class AnonymousRegistry implements FirewallRegistry
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
        if ($firewall->context()->isAnonymous()) {
            $firewall->addPostService('anonymous', $this->createService());

            $firewall->addProvider($this->createProvider());
        }

        return $make($firewall);
    }

    protected function createService(): callable
    {
        return function (Application $app, FirewallContext $context): Authentication {
            return new AnonymousAuthentication($context->anonymousKey());
        };
    }

    protected function createProvider(): callable
    {
        return function (Application $app, FirewallContext $context): AuthenticationProvider {
            return new ProvideAnonymousAuthentication($context->anonymousKey());
        };
    }
}