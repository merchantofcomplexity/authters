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
use Symfony\Component\HttpKernel\TerminableInterface;

final class ContextEventAware implements TerminableInterface
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

        return $next($request);
    }

    public function terminate(SymfonyRequest $request, Response $response)
    {
        if ($this->contextEvent && $request instanceof Request) {
            $token = $this->tokenStorage->getToken();

            if (!$token || $this->trustResolver->isAnonymous($token)) {
                $request->session()->forget($this->contextEvent->sessionName());
            } else {
                $request->session()->put($this->contextEvent->sessionName(), serialize($token));
            }

            // fixMe only way to keep session on terminable middleware
            $request->session()->save();
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