<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallProvision;

final class FirewallAware
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $preServices = [];

    /**
     * @var array
     */
    private $postServices = [];

    /**
     * @var array
     */
    private $services;

    /**
     * @var AuthenticationProviders
     */
    private $providers;

    /**
     * @var IdentityProviders
     */
    private $identityProviders;

    /**
     * @var FirewallContext
     */
    private $firewallContext;

    public function __construct(string $name, string ...$services)
    {
        $this->name = $name;
        $this->services = array_fill_keys(array_values($services), false);
        $this->providers = new AuthenticationProviders();
    }

    public function addService(string $serviceId, $service): FirewallAware
    {
        if (!isset($this->services[$serviceId])) {
            throw new InvalidArgumentException("Service id $serviceId does not exists in config");
        }

        $this->services[$serviceId] = $service;

        return $this;
    }

    public function addPreService(string $serviceId, callable $service): FirewallAware
    {
        $this->preServices[$serviceId] = $service;

        return $this;
    }

    public function addPostService(string $serviceId, callable $service): FirewallAware
    {
        $this->postServices[$serviceId] = $service;

        return $this;
    }

    public function resolveProvisionService(string $provisionKey, FirewallProvision $provision): void
    {
        if (!isset($this->services[$provisionKey])) {
            throw new InvalidArgumentException("provision service $provisionKey does not exists");
        }

        $this->services[$provision->serviceId()] = $provision;

        unset($this->services[$provisionKey]);
    }

    public function addProvider($provider): FirewallAware
    {
        $this->providers->add($provider);

        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function allServices(): array
    {
        foreach ($this->services as $serviceId => $resolved) {
            if (!$resolved) {
                throw new RuntimeException("Service id $serviceId has not been resolved");
            }
        }

        $services = array_merge($this->preServices, $this->getServices(), $this->postServices);

        if (!$services) {
            throw new InvalidArgumentException(
                "Authentication services for firewall {$this->name} can not be empty"
            );
        }

        return $services;
    }

    public function getProviders(): AuthenticationProviders
    {
        return $this->providers;
    }

    public function isFirewall(string $aName): bool
    {
        return $this->name === $aName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function context(): FirewallContext
    {
        return $this->firewallContext;
    }

    public function setContext(FirewallContext $firewallContext): void
    {
        $this->firewallContext = $firewallContext;
    }

    public function identityProviders(): IdentityProviders
    {
        return $this->identityProviders;
    }

    public function setIdentityProviders(IdentityProviders $identityProviders): void
    {
        $this->identityProviders = $identityProviders;
    }
}