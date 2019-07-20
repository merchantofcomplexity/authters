<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
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
     * @@return  Identity|LocalIdentity|IdentifierValue
     */
    public function getIdentity();

    public function getCredentials(): Credentials;

    public function getFirewallKey(): FirewallKey;

    public function isAuthenticated(): bool;

    public function setAuthenticated(bool $isAuthenticated): void;

    public function setAttribute(string $key, $value): void;

    public function removeAttribute(string $key): bool;

    public function hasAttribute(string $key): bool;

    public function getAttribute(string $key, $default = null);

    public function getAttributes(): array;
}