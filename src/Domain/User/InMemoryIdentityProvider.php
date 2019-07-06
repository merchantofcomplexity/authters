<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use function get_class;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;

final class InMemoryIdentityProvider implements IdentityProvider
{
    /**
     * @var Identity[]
     */
    private $identities;

    public function __construct(Identity ...$identities)
    {
        $this->identities = $identities;
    }

    public function requireIdentityOfIdentifier(IdentifierValue $identifier): Identity
    {
        foreach ($this->identities as $identity) {
            if ($identity->getIdentifier()->sameValueAs($identifier)) {
                return $identity;
            }
        }

        throw IdentityNotFound::forIdentity($identifier);
    }

    public function supportsIdentity(Identity $identity): bool
    {
        return get_class($identity) === InMemoryIdentity::class;
    }
}