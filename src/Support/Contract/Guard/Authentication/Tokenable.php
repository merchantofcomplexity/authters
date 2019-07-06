<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use Serializable;

interface Tokenable extends Serializable
{
    public function hasRoles(): bool;

    public function getRoles(): array;

    /**
     * @param Identity|LocalIdentity|IdentifierValue
     */
    public function setIdentity($identity): void;

    /**
     * @param Identity|LocalIdentity|IdentifierValue
     */
    public function getIdentity();

    public function getCredentials(): Credentials;

    public function isAuthenticated(): bool;

    public function setAuthenticated(bool $isAuthenticated): void;
}