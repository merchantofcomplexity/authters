<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Credentials\ClearPassword;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;

final class HttpBasicRequest implements IdentifierCredentialsRequest
{
    public function extractIdentifier(Request $request): IdentifierValue
    {
        return EmailIdentity::fromString($request->getUser());
    }

    public function extractPassword(Request $request): ClearCredentials
    {
        return new ClearPassword($request->getPassword());
    }

    public function extractCredentials(Request $request): array
    {
        return [
            $this->extractIdentifier($request),
            $this->extractPassword($request)
        ];
    }

    public function match(Request $request): bool
    {
        return true;
    }
}