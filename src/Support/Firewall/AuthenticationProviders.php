<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use Illuminate\Contracts\Container\Container;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
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

    public function add($authenticationProvider): void
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

        if (!$this->authenticationProviders) {
            throw new InvalidArgumentException("No authentication providers has been registered");
        }

        return $this->resolved = collect($this->authenticationProviders)
            ->transform(function ($provider) use ($container, $context): AuthenticationProvider {
                if (is_string($provider)) {
                    $provider = $container->get($provider);
                }

                return $provider($container, $context);
            })->toArray();
    }
}