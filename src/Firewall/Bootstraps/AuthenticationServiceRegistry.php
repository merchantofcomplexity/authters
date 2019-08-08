<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallProvision;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class AuthenticationServiceRegistry implements FirewallRegistry
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
        foreach ($firewall->getServices() as $serviceId => $loaded) {
            $service = $this->resolveService($serviceId, $loaded);

            $firewall->resolveProvisionService($serviceId, $service);
        }

        return $make($firewall);
    }


    /**
     * @param string $serviceId
     * @param $loaded
     * @return FirewallProvision|callable
     */
    protected function resolveService(string $serviceId, $loaded)
    {
        if (false !== $loaded) {
            return $loaded;
        }

        return $this->resolveFirewallProvision($serviceId);
    }

    protected function resolveFirewallProvision(string $serviceId): FirewallProvision
    {
        if (class_exists($serviceId) || $this->app->bound($serviceId)) {
            $service = $this->app->get($serviceId);

            if (!$service instanceof FirewallProvision) {
                throw new InvalidArgumentException(
                    "Service $serviceId must implement contract " . FirewallProvision::class);
            }

            return $service;
        }

        throw new InvalidArgumentException("Unable to resolve service $serviceId");
    }
}