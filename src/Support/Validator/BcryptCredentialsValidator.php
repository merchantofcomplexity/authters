<?php

namespace MerchantOfComplexity\Authters\Support\Validator;

use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsValidator;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;
use MerchantOfComplexity\Authters\Support\Value\Credentials\ClearPassword;

final class BcryptCredentialsValidator implements CredentialsValidator
{
    public function __invoke(BcryptEncodedPassword $password, ClearPassword $clearPassword): bool
    {
        return $password->verify($clearPassword);
    }

    public function supportsCredentials(EncodedCredentials $encodedCredentials, ClearCredentials $credentials): bool
    {
       return $encodedCredentials instanceof BcryptEncodedPassword && $credentials instanceof ClearPassword;
    }
}