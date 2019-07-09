<?php

namespace MerchantOfComplexity\Authters\Firewall\Factory;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Guardable;

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