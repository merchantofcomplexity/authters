<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface Authenticatable
{
    /**
     * Authenticate a token
     *
     * @param Tokenable $token
     * @return Tokenable
     */
    public function authenticate(Tokenable $token): Tokenable;
}