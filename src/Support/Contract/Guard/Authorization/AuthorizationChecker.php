<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

interface AuthorizationChecker
{
    /**
     * Check if a token, identity is granted to access resources
     *
     * @param array $attributes
     * @param object|null $subject
     * @return bool
     */
    public function isGranted(array $attributes, object $subject = null): bool;

    /**
     * Check if a token, identity is not granted to access resources
     *
     * @param array $attributes
     * @param object|null $subject
     * @return bool
     */
    public function isNotGranted(array $attributes, object $subject = null): bool;
}