<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

interface IdentifierCredentialsRequest extends AuthenticationRequest
{
    public function extractIdentifier(Request $request): IdentifierValue;

    public function extractPassword(Request $request): ClearCredentials;
}