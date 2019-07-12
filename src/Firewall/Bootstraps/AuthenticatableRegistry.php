<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Guard\Authentication\AuthenticationManager;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Firewall\AuthenticationProviders;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

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

    public function compose(FirewallAware $firewall, Closure $make)
    {
        $response = $make($firewall);

        $this->registerAuthenticationManager(
            $firewall->context(),
            $firewall->getProviders()
        );

        return $response;
    }

    protected function registerAuthenticationManager(FirewallContext $context, AuthenticationProviders $providers)
    {
        $this->app->bind(Authenticatable::class,
            function (Application $app) use ($context, $providers): Authenticatable {
                $authenticationProviders = $providers($app, $context);

                return new AuthenticationManager(...$authenticationProviders);
            });
    }
}