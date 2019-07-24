<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

interface IdentityChecker
{
    public function onPreAuthentication(Identity $identity): void;

    public function onPostAuthentication(Identity $identity): void;
}