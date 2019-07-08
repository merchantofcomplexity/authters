<?php

namespace MerchantOfComplexity\Authters\Firewall;

use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Exception\RuntimeException;

class FirewallManager
{
    /**
     * @var Application
     */
    private $app;

    private $middleware = [

    ];

    private $authProviders = [

    ];

    private $context = [

    ];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function make(string $firewallName)
    {
        return $this->middleware[$firewallName] = $this->middleware[$firewallName] ?? $this->buildService($firewallName);
    }

    protected function buildService(string $serviceId)
    {

    }

    public function addMiddleware(string $firewallName, string $serviceKey, callable $service): void
    {
        $this->middleware[$firewallName][$serviceKey] = $service;
    }

    public function registerAuthenticationProvider(string $contextKey, string $serviceKey, callable $service): void
    {
        $this->authProviders[$contextKey][$serviceKey] = $service;
    }

    public function determineServiceId(string $firewallName, string $serviceKey, string $serviceType): string
    {
        if (!in_array($serviceType, ['middleware', 'provider'])) {
            throw new RuntimeException("invalid service type");
        }

        return sprintf("firewall.%s_%s", $firewallName, $serviceKey);
    }
}