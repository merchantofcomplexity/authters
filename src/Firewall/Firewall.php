<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;

final class Firewall
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Application $app, Manager $manager)
    {
        $this->app = $app;
        $this->manager = $manager;
    }

    public function handle(Request $request, Closure $next, string $firewallName)
    {
        $authenticationServices = $this->manager->raise($firewallName, $request);

        return $this->startAuthentication($authenticationServices, $request, $next);
    }

    protected function startAuthentication(iterable $services, Request $request, Closure $next)
    {
        // fixMe temporary
        $iterator = array_filter(iterator_to_array($services));

        foreach ($iterator as $service) {
            $this->setGuardOnService($service);
        }

        // rewrite pipeline to accept generator
        // and default laravel middleware as we can add simple service from manager
        return (new Pipeline($this->app))
            ->via('authenticate')
            ->send($request)
            ->through($iterator)
            ->then(function () use ($request, $next) {
                return $next($request);
            });
    }

    protected function setGuardOnService($service): void
    {
        if ($service instanceof AuthenticationGuard) {
            $service->setGuard(
                $this->app->get(Guardable::class)
            );
        }

        if ($service instanceof StatefulAuthenticationGuard && $this->app->bound(Recallable::class)) {
            $service->setRecaller($this->app->get(Recallable::class));
        }
    }
}