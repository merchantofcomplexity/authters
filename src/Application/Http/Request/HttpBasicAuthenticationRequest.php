<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Value\Credentials\ClearPassword;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;

final class HttpBasicAuthenticationRequest implements AuthenticationRequest
{
    public function extractCredentials(Request $request)
    {
        return [
            EmailIdentity::fromString($request->getUser()),
            new ClearPassword($request->getPassword())
        ];
    }

    public function match(Request $request): bool
    {
        return true;
    }
}