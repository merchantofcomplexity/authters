<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Strategy;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\VoterCollection;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

final class ConsensusAuthorizationStrategy implements AuthorizationStrategy
{
    /**
     * @var bool
     */
    private $allowIfAllAbstain;

    /**
     * @var bool
     */
    private $allowIfEqual;

    /**
     * @var VoterCollection
     */
    private $voters;

    public function __construct(VoterCollection $voters, bool $allowIfAllAbstain, bool $allowIfEqual)
    {
        if ($voters->isEmpty()) {
            throw AuthenticationServiceFailure::noAuthorizationVoters();
        }

        $this->voters = $voters;
        $this->allowIfAllAbstain = $allowIfAllAbstain;
        $this->allowIfEqual = $allowIfEqual;
    }

    public function decide(Tokenable $token, array $attributes, object $subject = null): bool
    {
        $grant = 0;
        $deny = 0;

        foreach ($attributes as $attribute) {
            foreach ($this->voters->make() as $voter) {
                $decision = $voter->vote($token, [$attribute], $subject);

                switch ($decision) {
                    case Votable::ACCESS_GRANTED:
                        ++$grant;
                        break;

                    case Votable::ACCESS_DENIED:
                        ++$deny;
                        break;
                }
            }
        }

        if ($grant > $deny) {
            return true;
        }

        if ($deny > $grant) {
            return false;
        }

        return ($grant > 0) ? $this->allowIfEqual : $this->allowIfAllAbstain;
    }
}