<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Value\BearerToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;

final class BearerRequest implements AuthenticationRequest
{
    public function extractCredentials(Request $request)
    {
        return BearerToken::fromString($request->bearerToken());
    }

    public function match(Request $request): bool
    {
        return true;
    }
}