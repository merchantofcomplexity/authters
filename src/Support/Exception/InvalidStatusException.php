<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

class InvalidStatusException extends AuthenticationException
{
    public static function notEnabled(string $message = null): InvalidStatusException
    {
        return new self($message ?? 'Identity is not enabled');
    }
}