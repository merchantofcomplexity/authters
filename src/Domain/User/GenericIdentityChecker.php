<?php

namespace MerchantOfComplexity\Authters\Domain\User;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityStatus;
use MerchantOfComplexity\Authters\Support\Exception\IdentityExpired;
use MerchantOfComplexity\Authters\Support\Exception\IdentityLocked;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotEnabled;

class GenericIdentityChecker implements IdentityChecker
{
    public function onPreAuthentication(Identity $identity): void
    {
        if (!$identity instanceof IdentityStatus) {
            return;
        }

        if ($identity->isIdentityExpired()) {
            throw IdentityExpired::identifier($identity->getIdentifier());
        }
    }

    public function onPostAuthentication(Identity $identity): void
    {
        if (!$identity instanceof IdentityStatus) {
            return;
        }

        if (!$identity->isIdentityNonLocked()) {
            throw IdentityLocked::identifier($identity->getIdentifier());
        }

        if (!$identity->isIdentityEnabled()) {
            throw IdentityNotEnabled::identifier($identity->getIdentifier());
        }
    }
}