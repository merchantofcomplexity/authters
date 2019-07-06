<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class IdentityNotFound extends AuthenticationException
{
    public static function forIdentity(IdentifierValue $identity): self
    {
        return new self("Identity {$identity} not found");
    }

    public static function hideBadCredentials(BadCredentials $exception): self
    {
        return new self("Identity not found", 403, $exception);
    }
}