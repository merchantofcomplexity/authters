<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class BadCredentials extends AuthenticationException
{
    public static function invalid(): self
    {
        return new self("Invalid credentials");
    }

    public static function hasChanged(): self
    {
        return new self("Credentials has changed between session");
    }

    public static function emptyCredentials(): self
    {
        return new self("Credentials are empty");
    }
}