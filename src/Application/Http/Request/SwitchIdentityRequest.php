<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\SwitchIdentityRequest as BaseSwitchIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;
use MerchantOfComplexity\Authters\Support\Value\Identifier\NullIdentifier;

final class SwitchIdentityRequest implements BaseSwitchIdentity
{
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

        return EmailIdentity::fromString($request->get(self::IDENTIFIER_QUERY));
    }

    public function isExitIdentityRequest(Request $request): bool
    {
        return $request->query->has(self::EXIT_QUERY);
    }

    public function isSwitchIdentityRequest(Request $request): bool
    {
        return $request->query->has(self::IDENTIFIER_QUERY);
    }
}