<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use MerchantOfComplexity\Authters\Exception\RuntimeException;

trait HasTokenAttributes
{
    /**
     * @var array
     */
    private $attributes = [];

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key)
    {
        if (!$this->hasAttribute($key)) {
            throw new RuntimeException("Attribute $key does not exists in " . get_called_class());
        }
        
        return $this->attributes[$key];
    }

    public function removeAttribute(string $key): bool
    {
        if (!$this->hasAttribute($key)) {
            return false;
        }

        unset($this->attributes[$key]);

        return true;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}