<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Recaller;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericRecallerToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use Symfony\Component\HttpFoundation\Response;

final class SimpleRecallerService extends RecallableService
{
    protected function processAutoLogin(Recaller $recaller, Request $request): Tokenable
    {
        $recallerIdentifier = RecallerIdentifier::fromString($recaller->token());

        $refreshedIdentity = $this->refreshIdentity(
            $this->recallerProvider->requireIdentityOfRecaller($recallerIdentifier),
            $request
        );

        return new GenericRecallerToken($refreshedIdentity, $this->contextKey);
    }

    protected function onLoginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $this->refreshIdentity($token->getIdentity(), $request);
    }

    protected function refreshIdentity(Identity $identity, Request $request): Identity
    {
        $this->forgetCookie($request);

        $recallerIdentifier = RecallerIdentifier::nextIdentity();

        /** @var RecallerIdentity|Identity $refreshedIdentity */
        $refreshedIdentity = $this->recallerProvider->refreshIdentityRecaller($identity, $recallerIdentifier);

        $this->queueCookie(
            [
                $identity->getIdentifier()->identify(),
                $refreshedIdentity->getRecallerIdentifier()->identify()
            ]
        );

        return $refreshedIdentity;
    }
}