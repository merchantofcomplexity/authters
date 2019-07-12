<?php

namespace MerchantOfComplexity\Authters\Guard;


use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;

trait HasGuard
{
    /**
     * @var Guardable
     */
    protected $guard;

    public function setGuard(Guardable $guard): void
    {
        $this->guard = $guard;
    }

    public function hasGuard(): bool
    {
        return null !== $this->guard;
    }
}