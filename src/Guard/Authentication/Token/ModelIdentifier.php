<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use Illuminate\Database\Eloquent\Model;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use Serializable;

class ModelIdentifier implements Identity, Serializable
{
    /**
     * @var string
     */
    private $fqcnIdentityModel;

    /**
     * @var IdentifierValue
     */
    private $identifier;

    public function __construct(string $fqcnIdentityModel, IdentifierValue $identifier)
    {
        $this->fqcnIdentityModel = $fqcnIdentityModel;
        $this->identifier = $identifier;
    }

    public function getIdentifier(): IdentifierValue
    {
        return $this->identifier;
    }

    public function getFqcnIdentityModel(): string
    {
        return $this->fqcnIdentityModel;
    }

    public function newIdentityModelInstance(): ?Identity
    {
        /** @var Model $model */
        $model = new $this->fqcnIdentityModel;

        $identifier = $this->identifier->getValue();

        $attributes = is_array($identifier)
            ? $identifier
            : [$model->getKeyName() => $identifier];

        $model->setRawAttributes($attributes);

        return $model instanceof Identity ? null : $model;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function serialize()
    {
        return serialize([
            'fqcnIdentityModel' => $this->fqcnIdentityModel,
            'identifier' => $this->identifier
        ]);
    }

    public function unserialize($serialized)
    {
        [
            'fqcnIdentityModel' => $this->fqcnIdentityModel,
            'identifier' => $this->identifier
        ] = $serialized;
    }
}