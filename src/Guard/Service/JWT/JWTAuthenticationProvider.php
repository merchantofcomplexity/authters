<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Value\BearerToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class JWTAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var IdentityProvider
     */
    private $identityProvider;

    /**
     * @var JWTFactory
     */
    private $JWTFactory;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentityProvider $identityProvider,
                                JWTFactory $JWTFactory,
                                ContextKey $contextKey)
    {
        $this->identityProvider = $identityProvider;
        $this->JWTFactory = $JWTFactory;
        $this->contextKey = $contextKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        $credentials = $token->getCredentials();

        if (!$credentials instanceof BearerToken) {
            throw new InvalidArgumentException("Credentials must be a an instance of " . BearerToken::class);
        }

        $identifier = $this->JWTFactory->parseToken($credentials);

        $identity = $this->identityProvider->requireIdentityOfIdentifier($identifier);

        return new JWTToken($identity, $credentials, $this->contextKey, $identity->getRoles());
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof JWTToken && $token->getFirewallKey()->sameValueAs($this->contextKey);
    }
}