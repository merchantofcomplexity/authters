<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Response\EnforcerCredentialEntrypoint;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Validator\CredentialsValidator;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\BadCredentials;
use MerchantOfComplexity\Authters\Support\Value\Credentials\EmptyCredentials;
use Symfony\Component\HttpFoundation\Response;

class CredentialEnforcerAuthenticator
{
    const TOKEN_ATTRIBUTE = 'credential_enforcer';

    /**
     * @var EnforcerCredentialEntrypoint
     */
    private $entrypoint;

    /**
     * @var CredentialsValidator
     */
    private $credentialsValidator;

    /**
     * @var string
     */
    private $enforcerRoutePost;

    public function __construct(EnforcerCredentialEntrypoint $entrypoint,
                                CredentialsValidator $credentialsValidator,
                                string $enforcerRoutePost)
    {
        $this->entrypoint = $entrypoint;
        $this->credentialsValidator = $credentialsValidator;
        $this->enforcerRoutePost = $enforcerRoutePost;
    }

    public function enforceToken(LocalToken $token): LocalToken
    {
        $this->assertValidCredentials($token);

        if ($this->isTokenEnforced($token)) {
            throw new AuthenticationServiceFailure("token already enforced");
        }

        $token->setAttribute(self::TOKEN_ATTRIBUTE, true);

        return $token;
    }

    public function isTokenEnforced(LocalToken $token): bool
    {
        return true === $token->getAttribute(self::TOKEN_ATTRIBUTE, false);
    }

    public function isEnforcerForm(Request $request): bool
    {
        return $request->route()->getName() === $this->entrypoint->getRouteName();
    }

    public function isEnforcerPost(Request $request): bool
    {
        return $request->route()->getName() === $this->enforcerRoutePost;
    }

    public function matchEnforcerRoutes(Request $request): bool
    {
        return $this->isEnforcerForm($request) || $this->isEnforcerPost($request);
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        return $this->entrypoint->startAuthentication($request, $exception);
    }

    // fixMe copy of ProvideLocalAuthentication
    protected function assertValidCredentials(LocalToken $token): void
    {
        $identity = $token->getIdentity();

        /** @var ClearCredentials $presentedPassword */
        $presentedPassword = $token->getCredentials();

        if ($presentedPassword instanceof EmptyCredentials) {
            throw BadCredentials::emptyCredentials();
        }

        if (!is_callable($this->credentialsValidator)) {
            throw new RuntimeException("Credentials Validator must be a callable");
        }

        if (!$this->credentialsValidator->supportsCredentials($identity->getPassword(), $presentedPassword)) {
            throw new RuntimeException("Credentials Validator does not support credentials");
        }

        if (!($this->credentialsValidator)($identity->getPassword(), $presentedPassword)) {
            throw BadCredentials::invalid();
        }
    }
}