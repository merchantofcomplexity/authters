<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

final class DefaultTokenStorage implements TokenStorage
{
    /**
     * @var Tokenable
     */
    private $token;

    public function setToken(?Tokenable $token): void
    {
        $this->token = $token;
    }

    public function hasToken(): bool
    {
        return null !== $this->token;
    }

    public function clear(): bool
    {
        if ($this->token) {
            $this->token = null;

            return true;
        }

        return false;
    }

    public function getToken(): ?Tokenable
    {
        return $this->token;
    }
}