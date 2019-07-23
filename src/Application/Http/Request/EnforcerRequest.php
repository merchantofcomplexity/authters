<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexity\Authters\Support\Value\Credentials\ClearPassword;

final class EnforcerRequest implements AuthenticationRequest
{
    /**
     * @var string
     */
    private $credentialInput;

    /**
     * @var string[]
     */
    private $routesToProtect;

    public function __construct(string $credentialInput, string ...$routesToProtect)
    {
        $this->credentialInput = $credentialInput;
        $this->routesToProtect = $routesToProtect;
    }

    public function match(Request $request): bool
    {
        foreach ($this->routesToProtect as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @return ClearPassword
     * @throws AuthtersValueFailure
     */
    public function extractCredentials(Request $request): ClearPassword
    {
        return new ClearPassword($request->input($this->credentialInput));
    }
}