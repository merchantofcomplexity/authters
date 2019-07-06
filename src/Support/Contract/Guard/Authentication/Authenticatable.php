<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface Authenticatable
{
    public function authenticate(Tokenable $token): Tokenable;
}