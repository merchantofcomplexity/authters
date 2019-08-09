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
     * @return string[]
     */
    public function getRoleNames(): array;

    /**
     * Check the token has any roles
     *
     * @return bool
     */
    public function hasRoles(): bool;

    /**
     * Set an identity or identifier on token
     *
     * @param Identity|IdentifierValue
     */
    public function setIdentity($identity): void;

    /**
     * Return and identity or identifier
     *
     * @@return Identity|IdentifierValue
     */
    public function getIdentity();

    /**
     * Return credentials of token
     *
     * @return Credentials
     */
    public function getCredentials(): Credentials;

    /**
     * Return firewall key aka context key
     *
     * @return FirewallKey
     */
    public function getFirewallKey(): FirewallKey;

    /**
     * Check if the token is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * Set the token authenticated
     *
     * @param bool $isAuthenticated
     */
    public function setAuthenticated(bool $isAuthenticated): void;

    /**
     * Remove attribute by key
     *
     * @param string $key
     * @return bool
     */
    public function removeAttribute(string $key): bool;

    /**
     * Check the token contains attribute
     *
     * @param string $key
     * @return bool
     */
    public function hasAttribute(string $key): bool;

    /**
     * Set a token attribute
     *
     * @param string $key
     * @param $value
     */
    public function setAttribute(string $key, $value): void;

    /**
     * Return a token attribute
     *
     * @param string $key
     * @return mixed
     * @throws RuntimeException
     */
    public function getAttribute(string $key);

    /**
     * Return all attributes
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Override all token attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void;
}