<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SocialAuthenticationRequest;
use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Domain\User\Social\SocialProviderName;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\SocialToken;
use MerchantOfComplexity\Authters\Guard\Service\Social\SocialOAuthFactory;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Roles\SocialRoles;

final class SocialAuthenticator
{
    /**
     * @var SocialOAuthFactory
     */
    private $oAuthFactory;

    /**
     * @var SocialAuthenticationRequest
     */
    private $authenticationRequest;

    public function __construct(SocialOAuthFactory $oAuthFactory, SocialAuthenticationRequest $authenticationRequest)
    {
        $this->oAuthFactory = $oAuthFactory;
        $this->authenticationRequest = $authenticationRequest;
    }

    public function createRegistrationSocialToken(Request $request, ContextKey $contextKey): SocialToken
    {
        $provider = $this->extractProviderName($request);

        $socialIdentity = $this->oAuthFactory->socialIdentity($provider);

        if (!$socialIdentity instanceof IdentifierValue) {
            throw new AuthenticationServiceFailure("Invalid identifier");
        }

        return new SocialToken(
            $socialIdentity,
            $socialIdentity->getSocialCredentials(),
            $contextKey,
            [RoleValue::fromString(SocialRoles::NEED_REGISTRATION)]
        );
    }

    public function createLoginSocialToken(SocialToken $token): SocialToken
    {
        if ($token->getIdentity() instanceof IdentifierValue) {
            throw new AuthenticationServiceFailure("Invalid identifier");
        }

        $newToken = new SocialToken(
            $token->getIdentity(),
            $token->getCredentials(),
            $token->getFirewallKey(),
            [RoleValue::fromString(SocialRoles::LOGIN)]
        );

        $newToken->setAttributes($token->getAttributes());

        return $newToken;
    }

    public function extractProviderName(Request $request): SocialProviderName
    {
        return $this->authenticationRequest->extractCredentials($request);
    }

    public function socialRequest(): SocialAuthenticationRequest
    {
        return $this->authenticationRequest;
    }
}