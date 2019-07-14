<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

interface AuthorizationChecker
{
    public function isGranted(array $attributes, object $subject = null): bool;

    public function isNotGranted(array $attributes, object $subject = null): bool;
}