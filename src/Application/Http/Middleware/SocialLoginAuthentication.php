<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Response\SocialProviderEntrypoint;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SocialAuthenticator;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class SocialLoginAuthentication extends SocialAuthentication
{
    /**
     * @var SocialProviderEntrypoint
     */
    private $entrypoint;

    public function __construct(SocialAuthenticator $authenticator,
                                ContextKey $contextKey,
                                SocialProviderEntrypoint $entrypoint)
    {
        parent::__construct($authenticator, $contextKey);

        $this->entrypoint = $entrypoint;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $this->authenticator->extractProviderName($request);

            return $this->entrypoint->startAuthentication($request);
        } catch (Throwable $exception) {
            return $this->handleAuthenticationFailure($request, $exception);
        }
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty() && $this->authenticator->socialRequest()->isLogin($request);
    }
}