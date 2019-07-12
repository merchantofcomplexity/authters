<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use Symfony\Component\HttpFoundation\Response;

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

        if ($response = $this->startAuthentication($authenticationServices, $request)) {
            return $response;
        }

        return $next($request);
    }

    protected function startAuthentication(iterable $services, Request $request): ?Response
    {
        /** @var Authentication $service */
        foreach ($services as $service) {
            if (!$service) {
                continue;
            }

            if ($service instanceof AuthenticationGuard) {
                $guard = $this->app->get(Guardable::class);

                $service->setGuard($guard);
            }

            if ($this->serviceNeedRecaller($service)) {
                $service->setRecaller($this->app->get(Recallable::class));
            }

            if ($response = $service->authenticate($request)) {
                return $response;
            }
        }

        return null;
    }

    protected function serviceNeedGuard($service): bool
    {
        return $service instanceof AuthenticationGuard;
    }

    protected function serviceNeedRecaller($service): bool
    {
        return $service instanceof StatefulAuthenticationGuard && $this->app->bound(Recallable::class);
    }
}