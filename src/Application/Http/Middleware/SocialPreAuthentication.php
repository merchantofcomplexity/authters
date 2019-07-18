<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SocialPreAuthentication extends SocialAuthentication
{
    protected function processAuthentication(Request $request): ?Response
    {
        try {
            // first we create a token with a "need registration" role
            // if we succeed to authenticate the token, identity has been already registered
            // we store a new token with a "login" role
            // we keep workflow in both case to be handled in a endpoint
            $token = $this->authenticator->createRegistrationSocialToken($request, $this->contextKey);

            try {
                $token = $this->authenticator->createLoginSocialToken(
                    $this->guard->authenticateToken($token)
                );
            } catch (IdentityNotFound $notFound) {
                //
            }

            $this->guard->storage()->setToken($token);

            return null;
        } catch (Throwable $exception) {
            return $this->onException($request, $exception);
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty()
            && $this->authenticator->socialRequest()->isRedirect($request);
    }
}