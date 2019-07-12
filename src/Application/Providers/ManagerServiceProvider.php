<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Firewall\Manager;

abstract class ManagerServiceProvider extends ServiceProvider
{
    /**
     * '    firewall_name' => [
     *           ['service_id' => 'provision']
     *      ]
     * @var array
     */
    protected $services = [];

    /**
     * '    firewall_name' => [
     *           'provision'
     *      ]
     * @var array
     */
    protected $providers = [];

    public function boot(Manager $manager)
    {
        foreach ($this->services as $firewallName => $services) {
            foreach ($services as $service) {
                foreach ($service as $key => $value) {
                    $manager->addAuthenticationService($firewallName, $key, $value);
                }
            }
        }

        foreach ($this->providers as $firewallName => $providers) {
            foreach ($providers as $provider) {
                $manager->addAuthenticationProvider($firewallName, $provider);
            }
        }
    }
}