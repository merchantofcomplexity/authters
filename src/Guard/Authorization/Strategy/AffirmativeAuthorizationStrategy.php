<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Strategy;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

final class AffirmativeAuthorizationStrategy implements AuthorizationStrategy
{
    /**
     * @var bool
     */
    private $allowIfAllAbstain;

    /**
     * @var Votable[]
     */
    private $voters;

    public function __construct(bool $allowIfAllAbstain, Votable ...$voters)
    {
        if (0 === count($voters)) {
            throw AuthenticationServiceFailure::noAuthorizationVoters();
        }

        $this->voters = $voters;
        $this->allowIfAllAbstain = $allowIfAllAbstain;
    }

    public function decide(Tokenable $token, array $attributes, object $subject = null): bool
    {
        $deny = 0;

        foreach ($attributes as $attribute) {
            foreach ($this->voters as $voter) {
                $decision = $voter->vote($token, [$attribute], $subject);

                switch ($decision) {
                    case Votable::ACCESS_GRANTED:
                        return true;

                    case Votable::ACCESS_DENIED:
                        $deny++;
                }
            }
        }

        return ($deny > 0) ? false : $this->allowIfAllAbstain;
    }
}