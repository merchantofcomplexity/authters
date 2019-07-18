<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SocialAuthenticationRequest;
use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\SocialToken;
use MerchantOfComplexity\Authters\Guard\Service\Social\SocialOAuthFactory;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Roles\SocialRoles;

class SocialAuthenticator
{
    /**
     * @var SocialOAuthFactory
     */
    private $oAuthFactory;

    public function __construct(SocialOAuthFactory $oAuthFactory)
    {
        $this->oAuthFactory = $oAuthFactory;
    }

    public function createRegistrationSocialToken(Request $request, ContextKey $contextKey): SocialToken
    {
        $socialIdentity = $this->oAuthFactory->socialIdentity($request);

        return new SocialToken(
            $socialIdentity,
            $socialIdentity->getSocialCredentials(),
            $contextKey,
            [RoleValue::fromString(SocialRoles::NEED_REGISTRATION)]
        );
    }

    public function createLoginSocialToken(SocialToken $token): SocialToken
    {
        return new SocialToken(
            $token->getIdentity(),
            $token->getCredentials(),
            $token->getFirewallKey(),
            [RoleValue::fromString(SocialRoles::LOGIN)]
        );
    }

    public function isRedirect(Request $request): bool
    {
        return $this->oAuthFactory->authenticationRequest()->isRedirect($request);
    }

    public function isLogin(Request $request): bool
    {
        return $this->oAuthFactory->authenticationRequest()->isLogin($request);
    }

    public function extractCredentials(Request $request)
    {
        return $this->oAuthFactory->authenticationRequest()->extractCredentials($request);
    }
}