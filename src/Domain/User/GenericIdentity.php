<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;

class GenericIdentity implements LocalIdentity
{
    /**
     * @var array
     */
    private $user;

    public function __construct(array $user)
    {
        $this->user = $user;
    }

    public function getIdentifier(): IdentifierValue
    {
        return IdentityId::fromString($this->user['id']);
    }

    public function getPassword(): EncodedCredentials
    {
        return BcryptEncodedPassword::fromString($this->user['password']);
    }

    public function getRoles(): array
    {
        return $this->user['roles'] ?? [RoleValue::fromString('ROLE_USER')];
    }
}