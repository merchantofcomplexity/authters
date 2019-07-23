<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SocialRequest;
use MerchantOfComplexity\Authters\Guard\Service\Social\SocialOAuthFactory;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class SocialProviderEntrypoint implements Entrypoint
{
    /**
     * @var SocialOAuthFactory
     */
    private $oauthManager;

    /**
     * @var SocialRequest
     */
    private $authenticationRequest;

    public function __construct(SocialOAuthFactory $oauthManager, SocialRequest $authenticationRequest)
    {
        $this->oauthManager = $oauthManager;
        $this->authenticationRequest = $authenticationRequest;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        $provider = $this->authenticationRequest->extractCredentials($request);

        return $this->oauthManager->socialiteInstance($provider)->redirect();
    }
}