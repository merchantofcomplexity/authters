<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class RecallerAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var Recallable
     */
    private $recaller;

    public function __construct(Recallable $recaller)
    {
        $this->recaller = $recaller;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        if (!$recallerToken = $this->recaller->autoLogin($request)) {
            return null;
        }

        try {
            $this->fireAttemptLoginEvent($request, $recallerToken);

            $authenticatedToken = $this->guard->storeAuthenticatedToken($recallerToken);

            $this->fireSuccessLoginEvent($request, $authenticatedToken);

            return null;
        } catch (AuthenticationException $exception) {
            $this->recaller->loginFail($request);

            throw $exception;
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty();
    }
}