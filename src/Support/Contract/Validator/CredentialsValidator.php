<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Validator;

use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;

interface CredentialsValidator
{
    public function supportsCredentials(EncodedCredentials $encodedCredentials, ClearCredentials $credentials): bool;
}