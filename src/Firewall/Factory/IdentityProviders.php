<?php

namespace MerchantOfComplexity\Authters\Firewall\Factory;

use Generator;
use Illuminate\Contracts\Container\Container;

final class IdentityProviders
{
    /**
     * @var string[]
     */
    private $identityProviders;

    public function __construct(string ...$identityProviders)
    {
        $this->identityProviders = $identityProviders;
    }

    public function __invoke(Container $container): Generator
    {
        foreach ($this->identityProviders as $identityProvider) {
            yield $container->get($identityProvider);
        }
    }
}