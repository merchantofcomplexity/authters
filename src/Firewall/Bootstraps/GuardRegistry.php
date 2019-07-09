<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Firewall\Factory\Guard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Guardable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

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

    public function compose(Builder $auth, Closure $make)
    {
        /** @var Builder $auth */
        $response = $make($auth);

        $this->app->instance(Guardable::class, $this->newGuardInstance($auth->context()));

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