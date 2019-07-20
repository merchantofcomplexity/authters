<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Validator;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;

interface CredentialsChecker
{
    /**
     * @param Identity $identity
     * @param Tokenable $token
     * @throws BadCredentials
     */
    public function checkCredentials(Identity $identity, Tokenable $token): void;
}