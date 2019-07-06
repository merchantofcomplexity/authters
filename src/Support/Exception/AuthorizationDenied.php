<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class AuthorizationDenied extends AuthenticationException
{
    public static function reason(string $message = null): AuthorizationDenied
    {
        return new self($message ?? 'Authorization denied');
    }
}