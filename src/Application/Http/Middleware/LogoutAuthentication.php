<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Logout;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogout;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use Symfony\Component\HttpFoundation\Response;

abstract class LogoutAuthentication extends Authentication
{
    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var array
     */
    private $logoutHandlers;

    public function __construct(TrustResolver $trustResolver,
                                Logout ...$logoutHandlers)
    {
        $this->trustResolver = $trustResolver;
        $this->logoutHandlers = $logoutHandlers;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        $token = $this->guard->storage()->getToken();

        if (!$this->logoutHandlers) {
            throw AuthenticationServiceFailure::noLogoutHandler();
        }

        $response = $this->createLogoutRedirectResponse($request, $token);

        /** @var Logout $logoutHandler */
        foreach ($this->logoutHandlers as $logoutHandler) {
            $logoutHandler->logout($request, $token, $response);
        }

        $this->guard->clearStorage();

        $this->guard->fireAuthenticationEvent(new IdentityLogout($request, $token));

        return $response;
    }

    abstract protected function matchRequest(Request $request, Tokenable $token): bool;

    abstract protected function createLogoutRedirectResponse(Request $request, Tokenable $token): Response;

    public function addHandler(Logout $logoutHandler): void
    {
        $this->logoutHandlers[] = $logoutHandler;
    }

    protected function requireAuthentication(Request $request): bool
    {
        $token = $this->guard->storage()->getToken();

        return $token
            && !$this->trustResolver->isAnonymous($token)
            && $this->matchRequest($request, $token);
    }
}