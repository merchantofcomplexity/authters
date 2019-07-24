<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

final class ContextEventAware
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
     * @var ContextEvent
     */
    private $contextEvent;

    public function __construct(TokenStorage $tokenStorage, TrustResolver $trustResolver, Dispatcher $dispatcher)
    {
        $this->tokenStorage = $tokenStorage;
        $this->trustResolver = $trustResolver;
        $this->dispatcher = $dispatcher;
    }

    public function handle(Request $request, Closure $next)
    {
        $this->dispatcher->listen(ContextEvent::class, [$this, 'onContextEvent']);

        $response = $next($request);

        $this->dumbTerminateResponse($request, $response);

        return $response;
    }

    public function dumbTerminateResponse(SymfonyRequest $request,Response $response): void
    {
        if ($this->contextEvent && $request instanceof Request) {
            $token = $this->tokenStorage->getToken();

            if (!$token || $this->trustResolver->isAnonymous($token)) {
                $request->session()->forget($this->contextEvent->sessionName());
            } else {
                $request->session()->put($this->contextEvent->sessionName(), serialize($token));
            }
        }
    }

    public function onContextEvent(ContextEvent $contextEvent): void
    {
        if ($this->contextEvent) {
            throw new RuntimeException("Context event can run only once per request");
        }

        $this->contextEvent = $contextEvent;
    }
}