<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;

final class GenericTrustResolver implements TrustResolver
{
    /**
     * @var string
     */
    private $anonymous;

    /**
     * @var string
     */
    private $remembered;

    public function __construct(string $anonymous, string $remembered)
    {
        if(!interface_exists($anonymous) || !interface_exists($remembered)){
            $message = 'Generic implementation of trust resolver accept token interface only';

            throw new InvalidArgumentException($message);
        }

        $this->anonymous = $anonymous;
        $this->remembered = $remembered;
    }

    public function isFullyAuthenticated(?Tokenable $token): bool
    {
        if (!$token) {
            return false;
        }

        return !$this->isAnonymous($token) && !$this->isRemembered($token);
    }

    public function isAnonymous(?Tokenable $token): bool
    {
        return $token instanceof $this->anonymous;
    }

    public function isRemembered(?Tokenable $token): bool
    {
        return $token instanceof $this->remembered;
    }
}