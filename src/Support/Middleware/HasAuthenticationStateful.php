<?php

namespace MerchantOfComplexity\Authters\Support\Middleware;

trait HasAuthenticationStateful
{
    protected $recaller;

    public function setRecaller($recaller): void
    {
        $this->recaller = $recaller;
    }
}