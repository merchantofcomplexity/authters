<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;

class RoleVoter implements Votable
{
    const ROLE_PREFIX = RoleValue::PREFIX;

    public function vote(Tokenable $token, array $attributes, object $subject = null): int
    {
        $vote = self::ACCESS_ABSTAIN;

        $roles = $this->extractRoles($token);

        foreach ($attributes as $attribute) {
            if (!Str::startsWith($attribute, self::ROLE_PREFIX)) {
                continue;
            }

            $vote = self::ACCESS_DENIED;

            foreach ($roles as $role) {
                if ($attribute === $role) {
                    return self::ACCESS_GRANTED;
                }
            }
        }

        return $vote;
    }

    protected function extractRoles(Tokenable $token): array
    {
        return $token->getRoleNames();
    }
}