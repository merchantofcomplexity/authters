<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface Votable
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    public function vote(Tokenable $token, array $attributes, object $subject = null): int;
}