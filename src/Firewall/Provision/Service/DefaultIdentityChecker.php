<?php

namespace MerchantOfComplexity\Authters\Firewall\Provision\Service;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;

final class DefaultIdentityChecker implements IdentityChecker
{
    public function onPreAuthentication(Identity $user): void
    {
    }

    public function onPostAuthentication(Identity $user): void
    {
    }
}