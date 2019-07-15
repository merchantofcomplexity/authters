<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Strategy;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Guard\Authorization\Voters;

final class UnanimousAuthorizationStrategy implements AuthorizationStrategy
{
    /**
     * @var bool
     */
    private $allowIfAllAbstain;

    /**
     * @var Voters
     */
    private $voters;

    public function __construct(Voters $voters, bool $allowIfAllAbstain)
    {
        if ($voters->isEmpty()) {
            throw AuthenticationServiceFailure::noAuthorizationVoters();
        }

        $this->voters = $voters;
        $this->allowIfAllAbstain = $allowIfAllAbstain;
    }

    public function decide(Tokenable $token, array $attributes, object $subject = null): bool
    {
        $grant = 0;

        foreach ($attributes as $attribute) {
            /** @var Votable $voter */
            foreach ($this->voters->make() as $voter) {
                $decision = $voter->vote($token, [$attribute], $subject);

                switch ($decision) {
                    case Votable::ACCESS_GRANTED:
                        ++$grant;
                        break;

                    case Votable::ACCESS_DENIED:
                        return false;

                    default:
                        break;
                }
            }
        }

        return ($grant > 0) ?? $this->allowIfAllAbstain;
    }
}