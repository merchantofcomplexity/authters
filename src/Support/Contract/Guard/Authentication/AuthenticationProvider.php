<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface AuthenticationProvider extends Authenticatable
{
    /**
     * Check the token is supported by the authentication provider
     *
     * It could check by implementation, contract
     * and equality of a firewall key
     *
     * @param Tokenable $token
     * @return bool
     */
    public function supportToken(Tokenable $token): bool;
}