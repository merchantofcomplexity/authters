<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;

final class Authorization
{
    /**
     * @var AuthorizationStrategy
     */
    private $authorizationStrategy;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(AuthorizationStrategy $authorizationStrategy,
                                TokenStorage $tokenStorage,
                                array $attributes = [])
    {
        $this->authorizationStrategy = $authorizationStrategy;
        $this->tokenStorage = $tokenStorage;
        $this->attributes = $attributes;
    }

    public function handle(Request $request, Closure $next, ...$attributes)
    {
        if (!$token = $this->tokenStorage->getToken()) {
            throw AuthenticationServiceFailure::credentialsNotFound();
        }

        $attributes = array_merge($this->attributes, $attributes);

        if ($attributes && !$this->authorizationStrategy->decide($token, $attributes, $request)) {
            throw AuthorizationDenied::reason();
        }

        return $next($request);
    }

    public function mergeAttributes(array $attributes): void
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }
}