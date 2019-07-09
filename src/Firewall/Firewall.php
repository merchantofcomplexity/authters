<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Closure;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;

final class Firewall
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function handle(Request $request, Closure $next, string $firewallName)
    {
        /** @var Authentication $authenticationService */
        foreach ($this->manager->raise($firewallName, $request) as $authenticationService) {
            $response = $authenticationService->handle($request);

            if ($response) {
                return $response;
            }
        }

        return $next($request);
    }
}