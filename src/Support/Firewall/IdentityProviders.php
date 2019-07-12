<?php

namespace MerchantOfComplexity\Authters\Support\Firewall;

use Generator;
use Illuminate\Contracts\Container\Container;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;

final class IdentityProviders
{
    /**
     * @var string[]
     */
    private $identityProviders;

    public function __construct(string ...$identityProviders)
    {
        if (!$identityProviders) {
            throw new InvalidArgumentException("You must provide at least one identity provider");
        }

        $this->identityProviders = $identityProviders;
    }

    public function __invoke(Container $container): Generator
    {
        foreach ($this->identityProviders as $identityProvider) {
            yield $container->get($identityProvider);
        }
    }
}