<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextAuthentication;
use MerchantOfComplexity\Authters\Domain\User\RefreshTokenIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;
use MerchantOfComplexity\Authters\Support\Firewall\IdentityProviders;

final class ContextRegistry implements FirewallRegistry
{
    public function compose(FirewallAware $firewall, Closure $make)
    {
        if (!$firewall->context()->isStateless()) {
            $serializationContext = $this->createContext($firewall->getIdentityProviders());

            $firewall->addPreService('serialization', $serializationContext);
        }

        return $make($firewall);
    }

    protected function createContext(IdentityProviders $identityProviders): callable
    {
        return function (Application $app, FirewallContext $context) use ($identityProviders): Authentication {
            return new ContextAuthentication(
                new ContextEvent($context->contextKey()),
                new RefreshTokenIdentity($identityProviders($app))
            );
        };
    }
}