<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;

final class InMemoryIdentity implements LocalIdentity
{
    /**
     * @var array
     */
    private $identity;

    public function __construct(array $identity)
    {
        $this->identity = $identity;
    }

    public function getId(): IdentityId
    {
        return IdentityId::fromString($this->identity['id']);
    }

    public function getIdentifier(): IdentifierValue
    {
        return EmailIdentity::fromString($this->identity['email']);
    }

    public function getPassword(): EncodedCredentials
    {
        return BcryptEncodedPassword::fromString($this->identity['password']);
    }

    public function getRoles(): array
    {
        return $this->identity['roles'] ?? [RoleValue::fromString('ROLE_USER'),];
    }
}