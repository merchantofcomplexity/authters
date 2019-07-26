<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token;

use Illuminate\Database\Eloquent\Model;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\ModelIdentifier as BaseModelIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

class ModelIdentifier implements BaseModelIdentifier
{
    /**
     * @var string
     */
    private $identityModel;

    /**
     * @var IdentifierValue
     */
    private $identifier;

    public function __construct(string $IdentityModel, IdentifierValue $identifier)
    {
        $this->identityModel = $IdentityModel;
        $this->identifier = $identifier;
    }

    public function newIdentityModelInstance(): ?Identity
    {
        /** @var Model $model */
        $model = new $this->identityModel;

        $identifier = $this->identifier->getValue();

        $attributes = is_array($identifier)
            ? $identifier
            : [$model->getKeyName() => $identifier];

        $model->setRawAttributes($attributes);

        return $model instanceof Identity ? $model : null;
    }

    public function getIdentifier(): IdentifierValue
    {
        return $this->identifier;
    }

    public function getIdentityModel(): string
    {
        return $this->identityModel;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function serialize(): string
    {
        return serialize([
            $this->identityModel,
            $this->identifier
        ]);
    }

    public function unserialize($serialized): void
    {
        $serialized = is_array($serialized) ? $serialized : unserialize($serialized);

        [
            $this->identityModel,
            $this->identifier
        ] = $serialized;
    }
}