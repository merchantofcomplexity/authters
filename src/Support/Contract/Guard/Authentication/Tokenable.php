<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use Serializable;

interface Tokenable extends Serializable
{
    /**
     * @deprecated use getRoleNames
     * @return array
     */
    public function getRoles(): array;

    public function getRoleNames(): array;

    public function hasRoles(): bool;

    /**
     * @param Identity|IdentifierValue
     */
    public function setIdentity($identity): void;

    /**
     * @@return Identity|IdentifierValue
     */
    public function getIdentity();

    public function getCredentials(): Credentials;

    public function getFirewallKey(): FirewallKey;

    public function isAuthenticated(): bool;

    public function setAuthenticated(bool $isAuthenticated): void;

    public function removeAttribute(string $key): bool;

    public function hasAttribute(string $key): bool;

    public function setAttribute(string $key, $value): void;

    /**
     * @param string $key
     * @return mixed
     * @throws RuntimeException
     */
    public function getAttribute(string $key);

    public function getAttributes(): array;

    public function setAttributes(array $attributes): void;
}