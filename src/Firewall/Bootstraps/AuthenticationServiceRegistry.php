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
        $this->resolveServicesOfFirewall($firewall);

        return $make($firewall);
    }

    protected function resolveServicesOfFirewall(FirewallAware $firewall): void
    {
        foreach ($firewall->getServices() as $provisionId => $loaded) {
            if (class_exists($provisionId) || $this->app->bound($provisionId)) {
                $provision = $this->app->get($provisionId);

                if (!$provision instanceof FirewallProvision) {
                    throw new InvalidArgumentException(
                        "Service $provisionId must be an instance of " . FirewallProvision::class);
                }

                $firewall->resolveProvisionService($provisionId, $provision);
            }
        }
    }
}