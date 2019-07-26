<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\RefreshTokenIdentityStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\ModelIdentifier;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;

class RefreshTokenIdentity implements RefreshTokenIdentityStrategy
{
    /**
     * @var iterable
     */
    private $identityProviders;

    public function __construct(iterable $identityProviders)
    {
        $this->identityProviders = $identityProviders;
    }

    public function refreshTokenIdentity(Tokenable $token): ?Tokenable
    {
        $identity = $token->getIdentity();

        if (!$identity instanceof Identity) {
            return null;
        }

        if ($identity instanceof ModelIdentifier) {
            if (!$identity = $identity->newIdentityModelInstance()) {
                return null;
            }
        }

        try {
            $refreshedIdentity = $this
                ->firstSupportedIdentityProvider($identity)
                ->requireIdentityOfIdentifier($identity->getIdentifier());

            $token->setIdentity($refreshedIdentity);

            return $token;
        } catch (IdentityNotFound $exception) {
            return null;
        }
    }

    protected function firstSupportedIdentityProvider(Identity $identity): IdentityProvider
    {
        $found = false;

        /** @var IdentityProvider $identityProvider */
        foreach ($this->identityProviders as $identityProvider) {
            if (!$identityProvider->supportsIdentity($identity)) {
                $found = true;

                continue;
            }

            return $identityProvider;
        }

        if (!$found) {
            throw AuthenticationServiceFailure::noIdentityProvider();
        }

        throw AuthenticationServiceFailure::unsupportedIdentityProvider($identity);
    }
}