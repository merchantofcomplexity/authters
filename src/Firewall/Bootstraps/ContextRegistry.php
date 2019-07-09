<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextAuthentication;
use MerchantOfComplexity\Authters\Domain\User\RefreshTokenIdentity;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Firewall\Factory\IdentityProviders;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;

final class ContextRegistry implements FirewallRegistry
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
        if (!$auth->context()->isStateless()) {
            $serializationContext = $this->registerSerializationContext($auth->identityProviders());

            $auth->addRegistry($serializationContext);
        }

        return $make($auth);
    }

    protected function registerSerializationContext(IdentityProviders $identityProviders): callable
    {
        return function (Application $app, FirewallContext $context) use ($identityProviders): Authentication {
            return new ContextAuthentication(
                new ContextEvent($context->contextKey()),
                new RefreshTokenIdentity($identityProviders($app))
            );
        };
    }
}