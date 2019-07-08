<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericAnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication as BaseAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use Symfony\Component\HttpFoundation\Response;

final class AnonymousAuthentication implements BaseAuthentication
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

    public function handle(Request $request): ?Response
    {
        if (!$this->tokenStorage->hasToken()) {
            $this->tokenStorage->setToken(
                $this->authenticationManager->authenticate(new GenericAnonymousToken())
            );
        }

        return null;
    }
}