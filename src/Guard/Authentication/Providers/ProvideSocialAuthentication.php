<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\SocialToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\SocialIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

class ProvideSocialAuthentication implements AuthenticationProvider
{
    /**
     * @var IdentityProvider
     */
    private $provider;

    /**
     * @var IdentityChecker
     */
    private $identityChecker;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentityProvider $provider,
                                IdentityChecker $identityChecker,
                                ContextKey $contextKey)
    {
        $this->provider = $provider;
        $this->identityChecker = $identityChecker;
        $this->contextKey = $contextKey;
    }

    /**
     * @param Tokenable|SocialToken $token
     * @return Tokenable
     */
    public function authenticate(Tokenable $token): Tokenable
    {
        $identity = $this->retrieveIdentity($token);

        $this->identityChecker->onPreAuthentication($identity);

        $newToken = new SocialToken(
            $identity,
            $identity->getSocialCredentials(),
            $this->contextKey,
            $identity->getRoles()
        );

        $newToken->setAttributes($token->getAttributes());

        return $newToken;
    }

    protected function retrieveIdentity(SocialToken $token): SocialIdentity
    {
        $identifier = $token->getIdentity();

        if (!$identifier instanceof IdentifierValue && $identifier instanceof SocialIdentity) {
            return $identifier;
        }

        $identifier = $identifier->getIdentifier();

        $socialIdentity = $this->provider->requireIdentityOfIdentifier($identifier);

        if (!$socialIdentity instanceof SocialIdentity) {
            throw new AuthenticationServiceFailure(
                "Identity provider must return an implementation of " . SocialIdentity::class
            );
        }

        return $socialIdentity;
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof SocialToken && $token->getFirewallKey()->sameValueAs($this->contextKey);
    }
}