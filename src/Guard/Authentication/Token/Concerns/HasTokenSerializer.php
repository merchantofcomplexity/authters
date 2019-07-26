<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use Illuminate\Database\Eloquent\Model;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\ModelIdentifier;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use function get_class;
use function is_array;

trait HasTokenSerializer
{
    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        $this->__unserialize(is_array($serialized) ? $serialized : unserialize($serialized));
    }

    public function __unserialize(array $data): void
    {
        [
            'identity' => $this->identity,
            'is_authenticated' => $this->isAuthenticated,
            'role_names' => $this->roleNames,
            'attributes' => $this->attributes
        ] = $data;
    }

    public function toArray(): array
    {
        return [
            'identity' => $this->serialiazableIdentity($this->identity),
            'is_authenticated' => $this->isAuthenticated,
            'role_names' => $this->roleNames,
            'attributes' => $this->attributes
        ];
    }

    public function toJson($options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new RuntimeException(json_last_error_msg());
        }

        return $json;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return $this->toJson();
    }

    protected function serialiazableIdentity(Identity $identity): Identity
    {
        if ($identity instanceof Model) {
            return new ModelIdentifier(get_class($identity), $identity->getIdentifier());
        }

        return $identity;
    }
}