<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericAnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class ProvideAnonymousAuthentication implements AuthenticationProvider
{
    public function authenticate(Tokenable $token): Tokenable
    {
        return new GenericAnonymousToken();
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof GenericAnonymousToken;
    }
}