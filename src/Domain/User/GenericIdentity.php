<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;
use Serializable;

class GenericIdentity implements LocalIdentity, Serializable
{
    /**
     * @var IdentityId
     */
    private $id;

    /**
     * @var EncodedCredentials
     */
    private $password;

    /**
     * @var array
     */
    private $roles = [];

    public function __construct(array $identity)
    {
        foreach ($identity as $key => $value) {

            $property = Str::camel($key);

            if (!property_exists($this, $property)) {
                throw new RuntimeException("Property $key does not exist on class " . static::class);
            }

            $this->{$property} = $value;
        }
    }

    public function getId(): IdentityId
    {
        return IdentityId::fromString($this->id);
    }

    public function getIdentifier(): IdentifierValue
    {
        return $this->getId();
    }

    public function getPassword(): EncodedCredentials
    {
        return BcryptEncodedPassword::fromString($this->password);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function serialize()
    {
        return serialize([$this->id]);
    }

    public function unserialize($serialized)
    {
        [$this->id] = unserialize($serialized);
    }
}