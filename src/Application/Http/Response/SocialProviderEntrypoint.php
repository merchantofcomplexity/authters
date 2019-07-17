<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Http\Request;
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

    public function __construct(SocialOAuthFactory $oauthManager)
    {
        $this->oauthManager = $oauthManager;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        return $this->oauthManager->socialiteInstance($request)->redirect();
    }
}