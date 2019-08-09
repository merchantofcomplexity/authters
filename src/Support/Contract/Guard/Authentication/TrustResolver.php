<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface TrustResolver
{
    /**
     * Check if the token is fully authenticated
     *
     * @param Tokenable|null $token
     * @return bool
     */
    public function isFullyAuthenticated(?Tokenable $token): bool;

    /**
     * Check if the token is remembered type
     *
     * @param Tokenable|null $token
     * @return bool
     */
    public function isRemembered(?Tokenable $token): bool;

    /**
     * Check if the token is an anonymous type
     *
     * @param Tokenable|null $token
     * @return bool
     */
    public function isAnonymous(?Tokenable $token): bool;
}