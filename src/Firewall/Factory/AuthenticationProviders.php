<?php

namespace MerchantOfComplexity\Authters\Firewall\Factory;

use Illuminate\Contracts\Container\Container;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;

final class AuthenticationProviders
{
    /**
     * @var callable[]
     */
    private $authenticationProviders;

    /**
     * @var AuthenticationProvider[]
     */
    private $resolved;

    public function __construct(callable ...$authenticationProviders)
    {
        $this->authenticationProviders = $authenticationProviders;
    }

    public function add(callable $authenticationProvider): void
    {
        $this->authenticationProviders[] = $authenticationProvider;
    }

    /**
     * @param Container $container
     * @param FirewallContext $context
     * @return AuthenticationProvider[]
     */
    public function __invoke(Container $container, FirewallContext $context): array
    {
        // checkMe
        if ($this->resolved) {
            return $this->resolved;
        }

        return $this->resolved = collect($this->authenticationProviders)
            ->transform(function (callable $provider) use ($container, $context): AuthenticationProvider {
                return $provider($container, $context);
            })->toArray();
    }
}