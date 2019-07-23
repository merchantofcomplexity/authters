<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Response\CredentialEnforcerEntrypoint;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsChecker;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use Symfony\Component\HttpFoundation\Response;

class CredentialEnforcerAuthenticator
{
    const TOKEN_ATTRIBUTE = 'credential_enforcer';

    /**
     * @var CredentialEnforcerEntrypoint
     */
    private $entrypoint;

    /**
     * @var CredentialsChecker
     */
    private $credentialsChecker;

    /**
     * @var string
     */
    private $enforcerRoutePost;

    public function __construct(CredentialEnforcerEntrypoint $entrypoint,
                                CredentialsChecker $credentialsChecker,
                                string $enforcerRoutePost)
    {
        $this->entrypoint = $entrypoint;
        $this->credentialsChecker = $credentialsChecker;
        $this->enforcerRoutePost = $enforcerRoutePost;
    }

    public function enforceToken(LocalToken $token): LocalToken
    {
        $this->credentialsChecker->checkCredentials($token->getIdentity(), $token);

        if ($this->isTokenEnforced($token)) {
            throw new AuthenticationServiceFailure("token already enforced");
        }

        $token->setAttribute(self::TOKEN_ATTRIBUTE, true);

        return $token;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        return $this->entrypoint->startAuthentication($request, $exception);
    }

    public function isTokenEnforced(LocalToken $token): bool
    {
        if (!$token->hasAttribute(self::TOKEN_ATTRIBUTE)) {
            $token->setAttribute(self::TOKEN_ATTRIBUTE, false);
        }

        return true === $token->getAttribute(self::TOKEN_ATTRIBUTE);
    }

    public function isEnforcerFormRequest(Request $request): bool
    {
        return $request->route()->getName() === $this->entrypoint->getRouteName();
    }

    public function isEnforcerPostRequest(Request $request): bool
    {
        return $request->route()->getName() === $this->enforcerRoutePost;
    }

    public function matchEnforcerRoutes(Request $request): bool
    {
        return $this->isEnforcerFormRequest($request) || $this->isEnforcerPostRequest($request);
    }
}