<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

interface IdentityStatus extends Identity
{
    public function isIdentityExpired(): bool;

    public function isIdentityNonLocked(): bool;

    public function isIdentityEnabled(): bool;
}