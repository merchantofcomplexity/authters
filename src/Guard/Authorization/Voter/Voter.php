<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;

abstract class Voter implements Votable
{
    public function vote(Tokenable $token, array $attributes, object $subject = null): int
    {
        $vote = self::ACCESS_ABSTAIN;

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            $vote = self::ACCESS_DENIED;

            if ($this->voteOn($attribute, $token, $subject)) {
                return self::ACCESS_GRANTED;
            }
        }

        return $vote;
    }

    abstract protected function supports(string $attribute, object $subject): bool;

    abstract protected function voteOn(string $attribute, Tokenable $token, object $subject): bool;
}