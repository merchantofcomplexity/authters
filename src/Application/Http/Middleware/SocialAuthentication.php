<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Laravel\Socialite\Two\InvalidStateException;
use MerchantOfComplexity\Authters\Application\Http\Response\SocialProviderEntrypoint;
use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Firewall\Guard\HasEventGuard;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\SocialToken;
use MerchantOfComplexity\Authters\Guard\Service\Social\SocialOAuthFactory;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\AuthenticationEventGuard;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\IdentityNotFound;
use MerchantOfComplexity\Authters\Support\Roles\SocialRoles;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use function get_class;

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
                $token = $this->addRoles($token);

                try {
                    $token = $this->guard->storeAuthenticatedToken($token);
                    $token = $this->addRoles($token);
                } catch (IdentityNotFound $needRegistration) {
                    //
                } finally {
                    $this->guard->storage()->setToken($token);

                    return null;
                }
            }

            $this->oAuthFactory->authenticationRequest()->extractCredentials($request);

            return $this->redirectToSocialProvider->startAuthentication($request);
        } catch (Throwable $exception) {
            return $this->handleException($request, $exception);
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

    protected function addRoles(SocialToken $token): SocialToken
    {
        $roles = $token->getRoles();

        if ($token->getIdentity() instanceof IdentifierValue) {
            $roles[] = RoleValue::fromString(SocialRoles::NEED_REGISTRATION);
        } else[
            $roles[] = RoleValue::fromString(SocialRoles::LOGIN)
        ];

        return new SocialToken(
            $token->getIdentity(),
            $token->getCredentials(),
            $token->getFirewallKey(),
            $roles
        );
    }

    protected function handleException(Request $request, Throwable $exception): Response
    {
        if ($exception instanceof AuthenticationException) {
            return $this->onException($request, $exception);
        }

        $handled = [
            InvalidStateException::class,
            InvalidArgumentException::class,
            ClientException::class
        ];

        if (in_array(get_class($exception), $handled)) {
            $exception = new AuthenticationServiceFailure(
                "Authentication failed", 0, $exception
            );

            return $this->onException($request, $exception);
        }

        throw $exception;
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