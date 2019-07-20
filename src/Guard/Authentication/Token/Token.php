<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns\HasConstructorRoles;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns\HasTokenAttributes;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns\HasTokenIdentity;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns\HasTokenSerializer;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

abstract class Token implements Tokenable
{
    use HasConstructorRoles, HasTokenIdentity, HasTokenAttributes, HasTokenSerializer;

    /**
     * @var bool
     */
    private $isAuthenticated = false;

    public function setIdentity($user): void
    {
        $this->identity = $this->setTokenIdentity($user);
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function setAuthenticated(bool $isAuthenticated): void
    {
        $this->isAuthenticated = $isAuthenticated;
    }
}