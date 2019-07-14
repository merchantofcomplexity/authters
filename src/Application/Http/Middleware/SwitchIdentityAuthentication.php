<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SwitchIdentityAuthenticationRequest;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SwitchIdentityAuthenticator;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationGuard;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class SwitchIdentityAuthentication extends Authentication implements AuthenticationGuard
{
    use HasEventGuard;

    /**
     * @var SwitchIdentityAuthenticator
     */
    private $authenticator;

    /**
     * @var SwitchIdentityAuthenticationRequest
     */
    private $authenticationRequest;

    /**
     * @var bool
     */
    private $stateless;

    public function __construct(SwitchIdentityAuthenticator $authenticator, bool $stateless)
    {
        $this->authenticator = $authenticator;
        $this->stateless = $stateless;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $token = $this->authenticator->handleAuthentication($request, $this->guard->storage()->getToken());

        $this->guard->storage()->setToken($token);

        return $this->stateless ? null : $this->createRedirectResponse($request);
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->authenticator->isIdentityGranted($request);
    }

    protected function createRedirectResponse(Request $request): Response
    {
        $identifier = SwitchIdentityAuthenticationRequest::IDENTIFIER;
        $exit = SwitchIdentityAuthenticationRequest::EXIT;

        if ($request->query->has($identifier)) {
            $request->query->remove($identifier);
        }

        if ($request->query->has($exit)) {
            $request->query->remove($exit);
        }

        $request->server->set('QUERY_STRING', http_build_query($request->query->all()));

        return new RedirectResponse($request->getUri(), 302);
    }
}