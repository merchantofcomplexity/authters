<?php

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;

if (!function_exists('getToken')) {
    /**
     * @return Tokenable|null
     */
    function getToken(): ?Tokenable
    {
        return app(TokenStorage::class)->getToken();
    }
}

if (!function_exists('getIdentity')) {
    /**
     * @return Identity|null
     */
    function getIdentity(): ?Identity
    {
        $identity = getToken()->getIdentity();

        if ($identity instanceof Identity) {
            return $identity;
        }

        return null;
    }
}

if (!function_exists('isGranted')) {
    /**
     * @param string|array $attributes
     * @param object|null $subject
     * @return bool
     */
    function isGranted($attributes, object $subject = null): bool
    {
        return app(AuthorizationChecker::class)->isGranted((array)$attributes, $subject);
    }
}

if (!function_exists('isNotGranted')) {
    /**
     * @param string $attribute
     * @param object|null $subject
     * @return bool
     */
    function isNotGranted(string $attribute, object $subject = null): bool
    {
        return !isGranted($attribute, $subject);
    }
}
