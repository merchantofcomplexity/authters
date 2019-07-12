<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Guard\Guard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class GuardRegistry implements FirewallRegistry
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

        $guard = $this->newGuardInstance($firewall->context());

        $this->app->instance(Guardable::class, $guard);

        return $response;
    }

    protected function newGuardInstance(FirewallContext $context): Guardable
    {
        $entrypoint = $this->app->get($context->entryPointId());

        return new Guard(
            $this->app->get(TokenStorage::class),
            $this->app->get(Authenticatable::class),
            $this->app->get(Dispatcher::class),
            $entrypoint
        );
    }
}