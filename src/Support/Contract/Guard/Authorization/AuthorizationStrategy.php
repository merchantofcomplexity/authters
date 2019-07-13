<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface AuthorizationStrategy
{
    const AFFIRMATIVE = 'affirmative';
    const CONSENSUS = 'consensus';
    const UNANIMOUS = 'unanimous';

    public function decide(Tokenable $token, array $attributes, object $subject = null): bool;
}