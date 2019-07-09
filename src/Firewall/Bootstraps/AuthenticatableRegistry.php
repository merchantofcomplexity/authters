<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Guard\Authentication\AuthenticationManager;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;

final class AuthenticatableRegistry implements FirewallRegistry
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
        /** @var Builder $auth */
        $auth = $make($auth);

        $callback = $auth->authenticationProviders();
        $context = $auth->context();

        $this->app->bind(Authenticatable::class,
            function (Application $app) use ($callback, $context): Authenticatable {
                $providers = $callback($app, $context);

                return new AuthenticationManager(...$providers);
            });

        return $auth;
    }
}