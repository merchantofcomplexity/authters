<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SwitchIdentityRequest;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SwitchIdentityAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class SwitchIdentityAuthentication extends Authentication
{
    /**
     * @var SwitchIdentityAuthenticator
     */
    private $authenticator;

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
        $identifier = SwitchIdentityRequest::IDENTIFIER_QUERY;
        $exit = SwitchIdentityRequest::EXIT_QUERY;

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