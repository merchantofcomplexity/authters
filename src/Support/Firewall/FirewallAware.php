<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;

// checkMe class expose too much, probably get back to a passable factory to the pipeline
final class FirewallAware
{
    /**
     * @var string
     */
    private $firewallName;

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

    public function __construct(string $firewallName, array $definedServices)
    {
        $this->firewallName = $firewallName;
        $this->services = $definedServices;
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

    public function addProvider($provider): FirewallAware
    {
        $this->providers->add($provider);

        return $this;
    }

    public function getFirewallName(): string
    {
        return $this->firewallName;
    }

    protected function getServices(): array
    {
        foreach ($this->services as $serviceId => $callback) {
            if (!$callback) {
                $message = "Service id $serviceId has not been registered for firewall name {$this->firewallName}";

                throw new InvalidArgumentException($message);
            }
        }

        return $this->services;
    }

    public function allServices(): array
    {
        return array_merge($this->preServices, $this->getServices(), $this->postServices);
    }

    public function getProviders(): AuthenticationProviders
    {
        return $this->providers;
    }

    public function isFirewall(string $aName): bool
    {
        return $this->firewallName === $aName;
    }

    public function context(): FirewallContext
    {
        return $this->firewallContext;
    }

    public function setContext(FirewallContext $firewallContext): void
    {
        $this->firewallContext = $firewallContext;
    }

    public function getIdentityProviders(): IdentityProviders
    {
        return $this->identityProviders;
    }

    public function setIdentityProviders(IdentityProviders $identityProviders): void
    {
        $this->identityProviders = $identityProviders;
    }
}