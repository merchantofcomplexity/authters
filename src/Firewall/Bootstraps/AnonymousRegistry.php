<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Application\Http\Middleware\AnonymousAuthentication;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Guard\Authentication\Providers\ProvideAnonymousAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;

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

    public function compose(Builder $auth, Closure $make)
    {
        if ($auth->context()->isAnonymous()) {
            $auth->addRegistry(function (Application $app, FirewallContext $context): Authentication {
                return new AnonymousAuthentication($context->anonymousKey());
            });

            $auth->authenticationProviders()->add(
                function (Application $app, FirewallContext $context): AuthenticationProvider {
                    return new ProvideAnonymousAuthentication($context->anonymousKey());
                });
        }

        return $make($auth);
    }
}