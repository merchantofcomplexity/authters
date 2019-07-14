<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;
use MerchantOfComplexity\Authters\Support\Value\Identifier\NullIdentifier;

class SwitchIdentityAuthenticationRequest implements AuthenticationRequest
{
    const IDENTIFIER = '_switch_identity';
    const EXIT = '_exit_identity';

    public function match(Request $request): bool
    {
        if (!$request->isMethod('get')) {
            return false;
        }

        return $this->isExitIdentityRequest($request) || $this->isSwitchIdentityRequest($request);
    }

    public function extractCredentials(Request $request): IdentifierValue
    {
        if ($this->isExitIdentityRequest($request)) {
            return new NullIdentifier();
        }

        return EmailIdentity::fromString($request->get(self::IDENTIFIER));
    }

    public function isExitIdentityRequest(Request $request): bool
    {
        return $request->query->has(self::EXIT);
    }

    public function isSwitchIdentityRequest(Request $request): bool
    {
        return $request->query->has(self::IDENTIFIER);
    }
}