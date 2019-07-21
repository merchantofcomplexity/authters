<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Domain\Role\SwitchIdentityRole;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\SwitchIdentityToken;
use MerchantOfComplexity\Authters\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\SwitchIdentityAuthenticationRequest;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Events\IdentitySwitchAttempt;
use MerchantOfComplexity\Authters\Support\Events\IdentitySwitchExit;
use MerchantOfComplexity\Authters\Support\Events\IdentitySwitchSuccess;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;

final class SwitchIdentityAuthenticator
{
    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var IdentityProvider
     */
    private $identityProvider;

    /**
     * @var SwitchIdentityAuthenticationRequest
     */
    private $switchIdentityRequest;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(AuthorizationChecker $authorizationChecker,
                                IdentityProvider $identityProvider,
                                SwitchIdentityAuthenticationRequest $switchIdentityRequest,
                                Dispatcher $dispatcher,
                                ContextKey $contextKey)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->identityProvider = $identityProvider;
        $this->switchIdentityRequest = $switchIdentityRequest;
        $this->dispatcher = $dispatcher;
        $this->contextKey = $contextKey;
    }

    public function handleAuthentication(Request $request, Tokenable $currenToken): ?Tokenable
    {
        if ($this->switchIdentityRequest->isExitIdentityRequest($request)) {
            if ($exitToken = $this->attemptExitIdentity($currenToken)) {
                $this->dispatcher->dispatch(new IdentitySwitchExit($request, $exitToken));
            }

            return $exitToken;
        }

        if ($this->switchIdentityRequest->isSwitchIdentityRequest($request)) {
            $identifier = $this->switchIdentityRequest->extractCredentials($request);

            $this->dispatcher->dispatch(new IdentitySwitchAttempt($request, $currenToken));

            $switchedToken = $this->attemptSwitchIdentity($identifier, $currenToken);

            if ($switchedToken) {
                $this->dispatcher->dispatch(new IdentitySwitchSuccess($request, $switchedToken));
            }

            return $switchedToken;
        }

        return null;
    }

    protected function attemptExitIdentity(Tokenable $token): ?Tokenable
    {
        if (!$source = $this->extractOriginalToken($token)) {
            throw new AuthenticationServiceFailure("Original token from switch identity not found");
        }

        $identifier = $source->getIdentity()->getIdentifier();

        $source->setIdentity(
            $this->identityProvider->requireIdentityOfIdentifier($identifier)
        );

        return $source;
    }

    protected function attemptSwitchIdentity(IdentifierValue $identifier, Tokenable $token): ?SwitchIdentityToken
    {
        if ($source = $this->extractOriginalToken($token)) {
            throw new AuthorizationDenied("Already switch identity");
        }

        $this->assertIdentityAllowedToSwitch();

        return $this->createSwitchIdentityToken($identifier, $token);
    }

    protected function createSwitchIdentityToken(IdentifierValue $identifier, Tokenable $currentToken): SwitchIdentityToken
    {
        $identity = $this->identityProvider->requireIdentityOfIdentifier($identifier);

        if (!$identity instanceof LocalIdentity) {
            throw new AuthenticationServiceFailure("Can only switch from a local identity instance");
        }

        if ($identity->getIdentifier()->sameValueAs($currentToken->getIdentity()->getIdentifier())) {
            throw new AuthorizationDenied("Can not switch to your own identity");
        }

        $roles = array_merge($identity->getRoles(), [SwitchIdentityRole::fromSource($currentToken)]);

        return new SwitchIdentityToken($identity, $identity->getPassword(), $this->contextKey, $roles, $currentToken);
    }

    protected function extractOriginalToken(Tokenable $token): ?Tokenable
    {
        if ($token instanceof SwitchIdentityToken) {
            return $token->getOriginalToken();
        }

        return null;
    }

    protected function assertIdentityAllowedToSwitch(): void
    {
        if ($this->authorizationChecker->isNotGranted(['ROLE_ALLOWED_TO_SWITCH'])) {
            throw AuthorizationDenied::reason("Insufficient authorization to impersonate identity");
        }
    }

    public function isIdentityGranted(Request $request): bool
    {
        return $this->switchIdentityRequest->match($request)
            && (
                $this->authorizationChecker->isGranted([SwitchIdentityRole::NAME])
                || $this->authorizationChecker->isGranted(['is_fully_authenticated()'])
            );
    }
}
