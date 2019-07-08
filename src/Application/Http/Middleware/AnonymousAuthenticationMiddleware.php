<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericAnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;

final class AnonymousAuthenticationMiddleware
{
    /**
     * @var Authenticatable
     */
    private $authenticationManager;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(Authenticatable $authenticationManager, TokenStorage $tokenStorage)
    {
        $this->authenticationManager = $authenticationManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function handle(Request $request)
    {
        if (!$this->tokenStorage->hasToken()) {
            $this->tokenStorage->setToken(
                $this->authenticationManager->authenticate(new GenericAnonymousToken())
            );
        }

        return null;
    }
}