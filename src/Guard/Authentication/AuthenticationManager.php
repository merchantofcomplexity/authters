<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

final class AuthenticationManager implements Authenticatable
{
    /**
     * @var iterable
     */
    private $providers;

    public function __construct(AuthenticationProvider ...$providers)
    {
        if (0 === count($providers)) {
            throw AuthenticationServiceFailure::noAuthenticationProvider();
        }

        $this->providers = $providers;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        foreach ($this->providers as $provider) {
            if (!$provider->supportToken($token)) {
                continue;
            }

            return $provider->authenticate($token);
        }

        throw AuthenticationServiceFailure::unsupportedToken($token);
    }
}