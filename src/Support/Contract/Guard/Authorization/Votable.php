<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface Votable
{
    const ACCESS_GRANTED = 1;
    const ACCESS_ABSTAIN = 0;
    const ACCESS_DENIED = -1;

    /**
     * Return the vote of following constants
     * ACCESS_GRANTED, ACCESS_ABSTAIN or ACCESS_DENIED
     *
     * @param Tokenable $token
     * @param array $attributes
     * @param object|null $subject
     * @return int
     */
    public function vote(Tokenable $token, array $attributes, object $subject = null): int;
}