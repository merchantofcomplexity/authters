<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface AuthorizationStrategy
{
    /**
     * Affirmative authorization strategy
     *
     * it will grant on the first positive voter
     * unless all voters abstain, it will decide based on denied voters count
     * and finally decide according the "allow if all abstain" property
     */
    const AFFIRMATIVE = 'affirmative';

    /**
     * Affirmative authorization strategy
     *
     * it will decide based on comparison of denied count voters and positive count voters
     * unless all voters abstain, it will based decision on "allow if equals" property
     * and finally decide according the "allow if all abstain" property
     */
    const CONSENSUS = 'consensus';

    /**
     * Affirmative authorization strategy
     *
     * It will deny access on the first negative voter
     * unless all voters abstain, it will decide based on positive voters count
     * and finally decide according the "allow if all abstain" property

     * Default strategy for most applications
     */
    const UNANIMOUS = 'unanimous';

    /**
     * Check if token|identity is granted to access resources
     * based on an authorization strategy
     *
     * @param Tokenable $token
     * @param array $attributes
     * @param object|null $subject
     * @return bool
     */
    public function decide(Tokenable $token, array $attributes, object $subject = null): bool;
}