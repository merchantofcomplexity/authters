<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

trait HasTokenSerializer
{
    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        [
            $this->identity,
            $this->isAuthenticated,
            $this->roles
        ] = unserialize($serialized, [Tokenable::class]);
    }

    public function toArray(): array
    {
        return [
            $this->identity instanceof Identity ? $this->transformUser() : clone $this->identity,
            $this->isAuthenticated,
            array_map(function ($role) {
                return clone $role;
            }, $this->roles)
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

    /**
     * to override
     * @return Identity
     */
    protected function transformUser(): Identity
    {
        return clone $this->identity;
    }
}