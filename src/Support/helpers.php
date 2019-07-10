<?php

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;

if (!function_exists('getToken')) {
    function getToken(): Tokenable
    {
        return app(TokenStorage::class)->getToken();
    }
}

if (!function_exists('getIdentity')) {
    function getIdentity()
    {
        return getToken()->getIdentity();
    }
}

if (!function_exists('isGranted')) {
    function isGranted(Tokenable $token, array $attributes, object $subject = null): bool
    {
        return app(AuthorizationChecker::class)->isGranted($token, $attributes, $subject ?? request());
    }
}

if (!function_exists('isNotGranted')) {
    function isNotGranted(Tokenable $token, array $attributes, object $subject = null): bool
    {
        return !isGranted($token, $attributes, $subject);
    }
}
