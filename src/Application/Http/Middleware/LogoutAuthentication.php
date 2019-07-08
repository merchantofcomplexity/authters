<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Logout;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogout;
use Symfony\Component\HttpFoundation\Response;

abstract class LogoutAuthentication
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var TrustResolver
     */
    private $trustResolver;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $logoutHandlers;

    public function __construct(TokenStorage $tokenStorage,
                                TrustResolver $trustResolver,
                                Dispatcher $dispatcher,
                                Logout ...$logoutHandlers)
    {
        $this->tokenStorage = $tokenStorage;
        $this->trustResolver = $trustResolver;
        $this->dispatcher = $dispatcher;
        $this->logoutHandlers = $logoutHandlers;
    }

    public function handle(Request $request)
    {
        $token = $this->tokenStorage->getToken();

        if ($this->requireLogout($request, $token)) {
            $response = $this->createRedirectResponse($request, $token);

            /** @var Logout $logoutHandler */
            foreach ($this->logoutHandlers as $logoutHandler) {
                $logoutHandler->logout($token, $request, $response);
            }

            $this->tokenStorage->clear();

            $this->dispatcher->dispatch(new IdentityLogout($token, $request));

            return $response;
        }

        return null;
    }

    public function addHandler(Logout $logoutHandler): void
    {
        $this->logoutHandlers[] = $logoutHandler;
    }

    abstract protected function matchRequest(Request $request, Tokenable $token): bool;

    abstract protected function createRedirectResponse(Request $request, Tokenable $token): Response;

    protected function requireLogout(Request $request, ?Tokenable $token): bool
    {
        return $token
            && !$this->trustResolver->isAnonymous($token)
            && $this->matchRequest($request, $token);
    }
}