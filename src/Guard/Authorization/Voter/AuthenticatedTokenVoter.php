<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;

final class AuthenticatedTokenVoter implements Votable
{
    const FULLY = 'is_fully_authenticated_token';
    const REMEMBERED = 'is_remembered_token';
    const ANONYMOUSLY = 'is_anonymous_token';

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    public function __construct(TrustResolver $trustResolver)
    {
        $this->trustResolver = $trustResolver;
    }

    public function vote(Tokenable $token, array $attributes, object $subject = null): int
    {
        foreach ($attributes as $attribute) {
            if ($this->noMatch($attribute)) {
                continue;
            }

            return $this->isAuthenticated($token) ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }

    protected function isAuthenticated(Tokenable $token): bool
    {
        return $this->trustResolver->isFullyAuthenticated($token)
            || $this->trustResolver->isRemembered($token)
            || $this->trustResolver->isAnonymous($token);
    }

    protected function noMatch(string $attribute = null): bool
    {
        return null === $attribute || !in_array($attribute, [self::FULLY, self::REMEMBERED, self::ANONYMOUSLY]);
    }
}