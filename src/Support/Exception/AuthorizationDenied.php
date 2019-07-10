<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class AuthorizationDenied extends AuthorizationException
{
    public static function reason(string $message = null): AuthorizationDenied
    {
        return new self($message ?? 'Authorization denied');
    }
}