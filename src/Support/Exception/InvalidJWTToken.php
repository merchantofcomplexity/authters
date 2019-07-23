<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class InvalidJWTToken extends JWTException
{
    public static function invalidToken(): self
    {
        return new self("Validation failed for token");
    }

    public static function invalidSignature(): self
    {
        return new self("Validation signature failed for token");
    }

    public static function invalidClaims(): self
    {
        return new self("Invalid claims for token");
    }
}