<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

class AuthenticationServiceFailure extends AuthenticationException
{
    public static function unsupportedToken(Tokenable $token): self
    {
        $tokenClass = get_class($token);

        return new self("No authentication provider support token {$tokenClass}");
    }

    public static function unsupportedIdentityProvider(Identity $identity): self
    {
        $identityClass = get_class($identity);

        return new self("No identity provider support identity class {$identityClass}");
    }

    public static function noAuthenticationProvider(): self
    {
        return new self("No authentication provider has been provided to Authentication manager");
    }

    public static function noIdentityProvider(): self
    {
        return new self("No identity provider has been provided to context middleware");
    }

    public static function credentialsNotFound(): self
    {
        return new self("Credentials not found in storage");
    }

    public static function noAuthorizationVoters(): self
    {
        return new self("You must at least add one voter to the authorization strategy");
    }

    public static function noLogoutHandler(): self
    {
        return new self("You must at least add one logout handler to the authentication logout");
    }
}