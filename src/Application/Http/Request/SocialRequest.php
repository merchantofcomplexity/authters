<?php

namespace MerchantOfComplexity\Authters\Application\Http\Request;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Domain\User\Social\SocialProviderName;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\AuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexity\Authters\Support\Exception\SocialAuthenticationException;

class SocialRequest implements AuthenticationRequest
{
    /**
     * @var string
     */
    private $routeProviderParameter;

    /**
     * @var string
     */
    private $loginRouteName;

    public function __construct(string $routeProviderParameter, string $loginRouteName)
    {
        $this->routeProviderParameter = $routeProviderParameter;
        $this->loginRouteName = $loginRouteName;
    }

    public function match(Request $request): bool
    {
        if ($this->isRedirect($request)) {
            return true;
        }

        return $this->isLogin($request);
    }

    public function extractCredentials(Request $request): SocialProviderName
    {
        try {
            $provider = $request->route()->parameter($this->routeProviderParameter, null);

            return SocialProviderName::fromString($provider);
        } catch (AuthtersValueFailure $exception) {
            throw new SocialAuthenticationException("Invalid social provider authentication");
        }
    }

    public function isRedirect(Request $request): bool
    {
        return $this->isLogin($request)
            && $request->has('code')
            && $request->has('state');
    }

    public function isLogin(Request $request): bool
    {
        return $request->route()->getName() === $this->loginRouteName;
    }
}