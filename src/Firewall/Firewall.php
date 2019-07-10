<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\StatefulAuthenticationGuard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Guardable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;

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
        /** @var Authentication $service */
        foreach ($this->manager->raise($firewallName, $request) as $service) {
            if ($service instanceof AuthenticationGuard) {
                $guard = $this->app->get(Guardable::class);

                $service->setGuard($guard);
            }

            if (($service instanceof StatefulAuthenticationGuard && $this->app->bound(Recallable::class))) {
                $service->setRecaller($this->app->make(Recallable::class));
            }

            $response = $service->handle($request);

            if ($response) {
                return $response;
            }
        }

        return $next($request);
    }
}