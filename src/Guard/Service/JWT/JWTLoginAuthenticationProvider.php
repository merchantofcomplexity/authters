<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT;

use MerchantOfComplexity\Authters\Guard\Authentication\Providers\ProvideLocalAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class JWTLoginAuthenticationProvider implements AuthenticationProvider
{
    /**
     * @var ProvideLocalAuthentication
     */
    private $provider;

    /**
     * @var JWTFactory
     */
    private $factory;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(ProvideLocalAuthentication $provider,
                                JWTFactory $factory,
                                ContextKey $contextKey)
    {
        $this->provider = $provider;
        $this->factory = $factory;
        $this->contextKey = $contextKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        $token = $this->provider->authenticate($token);

        $tokenCredential = $this->factory->createTokenCredential($token->getIdentity()->getIdentifier());

        return new JWTToken($token->getIdentity(), $tokenCredential, $this->contextKey, $token->getIdentity()->getRoles());
    }

    public function supportToken(Tokenable $token): bool
    {
        return $this->provider->supportToken($token);
    }
}