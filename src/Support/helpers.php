<?php

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;

if (!function_exists('getToken')) {
    function getToken(): ?Tokenable
    {
        return app(TokenStorage::class)->getToken();
    }
}

if (!function_exists('getIdentity')) {
    function getIdentity(): ?Identity
    {
        $identity =  getToken()->getIdentity();
        if($identity instanceof Identity){
            return $identity;
        }

        return null;
    }
}

if (!function_exists('isGranted')) {
    function isGranted(array $attributes, object $subject = null): bool
    {
        return app(AuthorizationChecker::class)->isGranted($attributes, $subject);
    }
}

if (!function_exists('isNotGranted')) {
    function isNotGranted(string $attribute, object $subject = null): bool
    {
        return !isGranted([$attribute], $subject);
    }
}
