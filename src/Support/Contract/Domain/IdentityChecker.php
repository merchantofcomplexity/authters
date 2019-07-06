<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

interface IdentityChecker
{
    public function onPreAuthentication(Identity $user): void;

    public function onPostAuthentication(Identity $user): void;
}