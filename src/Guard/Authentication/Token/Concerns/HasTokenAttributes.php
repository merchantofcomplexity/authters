<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

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

    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
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