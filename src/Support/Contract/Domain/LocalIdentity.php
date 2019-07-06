<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

use MerchantOfComplexity\Authters\Support\Contract\Value\EncodedCredentials;

interface LocalIdentity extends Identity
{
    public function getPassword(): EncodedCredentials;
}