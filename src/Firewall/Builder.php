<?php

namespace MerchantOfComplexity\Authters\Firewall;

use MerchantOfComplexity\Authters\Firewall\Factory\AuthenticationProviders;
use MerchantOfComplexity\Authters\Firewall\Factory\IdentityProviders;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;

final class Builder
{
    /**
     * @var array
     */
    private $registries = [];

    /**
     * @var FirewallContext
     */
    private $firewallContext;

    /**
     * @var IdentityProviders
     */
    private $identityProviders;

    /**
     * @var AuthenticationProviders
     */
    private $authenticationProviders;

    /**
     * @var callable[]
     */
    private $authenticationServices;

    public function __construct(FirewallContext $firewallContext,
                                IdentityProviders $identityProviders,
                                AuthenticationProviders $authenticationProviders,
                                callable ...$authenticationServices)
    {
        $this->firewallContext = $firewallContext;
        $this->identityProviders = $identityProviders;
        $this->authenticationProviders = $authenticationProviders;
        $this->authenticationServices = $authenticationServices;
    }

    public function addRegistry(callable $authenticationService): void
    {
        $this->registries[] = $authenticationService;
    }

    public function context(): FirewallContext
    {
        return $this->firewallContext;
    }

    public function identityProviders(): IdentityProviders
    {
        return $this->identityProviders;
    }

    public function authenticationProviders(): AuthenticationProviders
    {
        return $this->authenticationProviders;
    }

    public function services(): array
    {
        return $this->authenticationServices;
    }

    public function getRegistries(): array
    {
        return $this->registries;
    }
}