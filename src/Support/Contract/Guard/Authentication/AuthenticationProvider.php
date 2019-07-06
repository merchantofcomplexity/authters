<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface AuthenticationProvider extends Authenticatable
{
    public function supportToken(Tokenable $token): bool;
}