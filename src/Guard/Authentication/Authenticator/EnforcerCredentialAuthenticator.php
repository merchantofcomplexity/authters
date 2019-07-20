<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Response\EnforcerCredentialEntrypoint;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class EnforcerCredentialAuthenticator
{


    /**
     * @var EnforcerCredentialEntrypoint
     */
    private $entrypoint;

    /**
     * @var string
     */
    private $enforcerRoutePost;

    public function __construct(EnforcerCredentialEntrypoint $entrypoint, string $enforcerRoutePost)
    {
        $this->entrypoint = $entrypoint;
        $this->enforcerRoutePost = $enforcerRoutePost;
    }

    public function enforceToken(LocalToken $token): LocalToken
    {

    }

    public function isTokenEnforced(LocalToken $token): bool
    {

    }

    public function isEnforcerForm(Request $request): bool
    {
        return $request->route()->getName() === $this->entrypoint->getRouteName();
    }

    public function isEnforcerPost(Request $request): bool
    {
        return $request->is($this->enforcerRoutePost);
    }

    public function matchEnforcerRoutes(Request $request): bool
    {
        return $this->isEnforcerForm($request) || $this->isEnforcerPost($request);
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        return $this->entrypoint->startAuthentication($request, $exception);
    }
}