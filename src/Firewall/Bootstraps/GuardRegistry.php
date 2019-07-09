<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Firewall\Builder;
use MerchantOfComplexity\Authters\Firewall\Factory\Guard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
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
        $auth = $make($auth);

        $guard = $this->newGuardInstance($auth->context());

        $this->app->resolving(AuthenticationGuard::class,
            function (Application $app, AuthenticationGuard $middleware) use ($guard): void {
                if (!$middleware->hasGuard()) {
                    $middleware->setGuard($guard);
                }
            });

        return $auth;
    }

    protected function newGuardInstance(FirewallContext $context): Guardable
    {
        $entrypoint = $this->app->make($context->entryPointId());

        return new Guard(
            $this->app->make(TokenStorage::class),
            $this->app->make(Authenticatable::class),
            $this->app->make(Dispatcher::class),
            $entrypoint
        );
    }
}