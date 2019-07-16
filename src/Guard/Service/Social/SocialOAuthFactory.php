<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Social;

use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use MerchantOfComplexity\Authters\Application\Http\Request\SocialAuthenticationRequest;
use MerchantOfComplexity\Authters\Domain\User\Social\SocialProviderName;
use MerchantOfComplexity\Authters\Support\Contract\Domain\SocialIdentity;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;

final class SocialOAuthFactory
{
    /**
     * @var Factory
     */
    private $socialite;

    /**
     * @var SocialAuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var callable
     */
    private $identityTransformer;

    public function __construct(Factory $socialite,
                                SocialAuthenticationRequest $authenticationRequest,
                                callable $identityTransformer)
    {
        $this->socialite = $socialite;
        $this->authenticationRequest = $authenticationRequest;
        $this->identityTransformer = $identityTransformer;
    }

    public function socialIdentity(Request $request): SocialIdentity
    {
        if (!$this->authenticationRequest->isRedirect($request)) {
            throw BadCredentials::invalid();
        }

        $providerName = $this->extractSocialProvider($request);

        return ($this->identityTransformer)(
            $providerName,
            $this->socialiteUser($request)
        );
    }

    public function socialiteInstance($request): SocialiteProvider
    {
        return $this->socialite->driver(
            $this->extractSocialProvider($request)->getValue()
        );
    }

    public function extractSocialProvider(Request $request): SocialProviderName
    {
        return $this->authenticationRequest->extractCredentials($request);
    }

    protected function socialiteUser(Request $request): SocialiteUser
    {
        return $this->socialiteInstance($request)->user();
    }

    public function authenticationRequest(): SocialAuthenticationRequest
    {
        return $this->authenticationRequest;
    }
}