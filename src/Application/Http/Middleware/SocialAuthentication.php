<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Laravel\Socialite\Two\InvalidStateException;
use MerchantOfComplexity\Authters\Application\Http\Response\SocialProviderEntrypoint;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\SocialToken;
use MerchantOfComplexity\Authters\Guard\Service\Social\SocialOAuthFactory;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;
use Symfony\Component\HttpFoundation\Response;

class SocialAuthentication extends Authentication implements AuthenticationEventGuard
{
    use HasEventGuard;

    /**
     * @var SocialOAuthFactory
     */
    private $oAuthFactory;

    /**
     * @var SocialProviderEntrypoint
     */
    private $redirectToSocialProvider;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(SocialOAuthFactory $oAuthFactory,
                                SocialProviderEntrypoint $redirectToSocialProvider,
                                ContextKey $contextKey)
    {
        $this->oAuthFactory = $oAuthFactory;
        $this->redirectToSocialProvider = $redirectToSocialProvider;
        $this->contextKey = $contextKey;
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            if ($this->oAuthFactory->authenticationRequest()->isRedirect($request)) {
                $token = $this->createSocialToken($request);

                try {
                    $token = $this->guard->storeAuthenticatedToken($token);
                } catch (IdentityNotFound $needRegistration) {
                    // keep workflow
                } finally {
                    $this->guard->storage()->setToken($token);

                    return null;
                }
            }

            $this->oAuthFactory->authenticationRequest()->extractCredentials($request);

            // fixMe exception from entrypoint should be optional
            $auth = new AuthenticationException('foo');

            return $this->redirectToSocialProvider->startAuthentication($request, $auth);
        } catch (AuthenticationException $exception) {
            return $this->onException($request, $exception);
        } catch (InvalidStateException | InvalidArgumentException |ClientException $exception) {
            $exception = new AuthenticationServiceFailure("Authentication failed", 0, $exception);

            return $this->onException($request, $exception);
        }
    }

    protected function createSocialToken(Request $request): SocialToken
    {
        $socialIdentity = $this->oAuthFactory->socialIdentity($request);

        return new SocialToken(
            $socialIdentity,
            $socialIdentity->getSocialCredentials(),
            $this->contextKey
        );
    }

    protected function onException(Request $request, AuthenticationException $exception): Response
    {
        $this->guard->clearStorage();

        return $this->guard->startAuthentication($request, $exception);
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty()
            && $this->oAuthFactory->authenticationRequest()->match($request);
    }
}