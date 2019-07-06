<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

class AuthenticationServiceFailure extends AuthenticationException
{
    public static function unsupportedToken(Tokenable $token): self
    {
        $tokenClass = get_class($token);

        return new self("No authentication provider support token {$tokenClass}");
    }

    public static function noAuthenticationProvider(): self
    {
        return new self("No authentication provider has been provided to Authentication manager");
    }

    public static function credentialsNotFound(): self
    {
        return new self("Credentials not found in storage");
    }

    public static function noAuthorizationVoters(): self
    {
        return new self("You must at least add one voter to the authorization strategy");
    }
}