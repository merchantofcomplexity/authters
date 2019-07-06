<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationMiddleware as BaseMiddleware;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;
use MerchantOfComplexity\Authters\Support\Middleware\HasAuthenticationMiddleware;

final class AuthorizationMiddleware implements BaseMiddleware
{
    use HasAuthenticationMiddleware;

    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(AuthorizationChecker $authorizationChecker, array $attributes = [])
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->attributes = $attributes;
    }

    public function handle(Request $request, Closure $next, ...$attributes)
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw AuthenticationServiceFailure::credentialsNotFound();
        }

        $attributes = array_merge($this->attributes, $attributes);

        if ($attributes && !$this->authorizationChecker->isGranted($token, $attributes, $request)) {
            throw AuthorizationDenied::reason();
        }

        return $next($request);
    }

    public function mergeAttributes(array $attributes): void
    {
        $this->attributes = array_merge($this->attributes, $attributes);
    }
}