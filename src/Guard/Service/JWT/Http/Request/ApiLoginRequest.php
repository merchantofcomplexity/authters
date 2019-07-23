<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;

final class ApiLoginRequest implements IdentifierCredentialsRequest
{
    /**
     * @var IdentifierCredentialsRequest
     */
    private $loginRequest;

    public function __construct(IdentifierCredentialsRequest $loginRequest)
    {
        $this->loginRequest = $loginRequest;
    }

    public function match(Request $request): bool
    {
        return null === $request->bearerToken();
    }

    public function extractCredentials(Request $request): array
    {
        return $this->loginRequest->extractCredentials($request);
    }

    public function extractIdentifier(Request $request): IdentifierValue
    {
        return $this->loginRequest->extractIdentifier($request);
    }

    public function extractPassword(Request $request): ClearCredentials
    {
        return $this->loginRequest->extractPassword($request);
    }
}