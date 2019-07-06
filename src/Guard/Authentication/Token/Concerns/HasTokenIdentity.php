<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

trait HasTokenIdentity
{
    /**
     * @var Identity|LocalIdentity|IdentifierValue
     */
    private $identity;

    /**
     * @param $identity
     * @return Identity|LocalIdentity|IdentifierValue
     */
    private function setTokenIdentity($identity)
    {
        if (!$identity instanceof Identity && !$identity instanceof IdentifierValue) {
            $message = "User must implement ";
            $message .= Identity::class . " or ";
            $message .= IdentifierValue::class;

            throw new InvalidArgumentException($message);
        }

        return $identity;
    }

    private function identityHasChanged($identity): void
    {

    }
}