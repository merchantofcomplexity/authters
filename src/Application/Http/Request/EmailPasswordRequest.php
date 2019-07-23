<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Value\Credentials\ClearPassword;
use MerchantOfComplexity\Authters\Support\Value\Identifier\EmailIdentity;

final class EmailPasswordRequest implements IdentifierCredentialsRequest
{
    /**
     * @var string
     */
    private $loginRouteName;

    /**
     * @var string
     */
    private $identifierParameter;

    /**
     * @var string
     */
    private $passwordParameter;

    public function __construct(string $loginRouteName,
                                string $identifierParameter = 'identifier',
                                string $passwordParameter = 'credentials')
    {
        $this->loginRouteName = $loginRouteName;
        $this->identifierParameter = $identifierParameter;
        $this->passwordParameter = $passwordParameter;
    }

    public function match(Request $request): bool
    {
        return $request->route()->getName() === $this->loginRouteName;
    }

    public function extractCredentials(Request $request): array
    {
        return [
            $this->extractIdentifier($request),
            $this->extractPassword($request)
        ];
    }

    public function extractIdentifier(Request $request): IdentifierValue
    {
        return EmailIdentity::fromString($request->input($this->identifierParameter));
    }

    public function extractPassword(Request $request): ClearCredentials
    {
        return new ClearPassword($request->input($this->passwordParameter));
    }
}