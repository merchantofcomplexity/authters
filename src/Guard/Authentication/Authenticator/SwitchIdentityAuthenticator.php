<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Authenticator;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Request\SwitchIdentityAuthenticationRequest;
use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Domain\Role\SwitchIdentityRole;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentityEmail;
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
    private $authenticationRequest;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(AuthorizationChecker $authorizationChecker,
                                IdentityProvider $identityProvider,
                                SwitchIdentityAuthenticationRequest $authenticationRequest,
                                ContextKey $contextKey)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->identityProvider = $identityProvider;
        $this->authenticationRequest = $authenticationRequest;
        $this->contextKey = $contextKey;
    }

    public function handleAuthentication(Request $request, Tokenable $currenToken): ?Tokenable
    {
        if ($this->authenticationRequest->isExitIdentityRequest($request)) {
            return $this->attemptExitIdentity($currenToken);
        }

        if ($this->authenticationRequest->isSwitchIdentityRequest($request)) {
            $identifier = $this->authenticationRequest->extractCredentials($request);

            if (!$identifier instanceof IdentityEmail) {
                throw new AuthenticationServiceFailure("Switch identity allow for email identifier only");
            }

            return $this->attemptSwitchIdentity($identifier, $currenToken);
        }

        return null;
    }

    protected function attemptExitIdentity(Tokenable $token): ?Tokenable
    {
        if (!$source = $this->extractOriginalToken($token)) {
            throw new AuthenticationServiceFailure("Original token from switch identity not found");
        }

        $identity = $this->identityProvider->requireIdentityOfIdentifier($source->getIdentity()->getIdentifier());

        $source->setIdentity($identity);

        // dispatch event

        return $source;
    }

    protected function attemptSwitchIdentity(IdentityEmail $identifier, Tokenable $token): ?Tokenable
    {
        if ($current = $this->currentIdentity($identifier, $token)) {
            return $current;
        }

        $this->assertIdentityAllowedToSwitch();

        return $this->createSwitchIdentityToken($identifier, $token);
    }

    protected function currentIdentity(IdentifierValue $identifier, Tokenable $token): ?Tokenable
    {
        $source = $this->extractOriginalToken($token);

        if (!$source) {
            return null;
        }

        if ($token->getIdentity()->getIdentifier()->sameValueAs($identifier)) {
            return $token;
        }

        throw new AuthorizationDenied("Already switch identity");
    }

    protected function createSwitchIdentityToken(IdentifierValue $identifier, Tokenable $currentToken): Tokenable
    {
        $identity = $this->identityProvider->requireIdentityOfIdentifier($identifier);

        if (!$identity instanceof LocalIdentity) {
            throw new AuthenticationServiceFailure("Can only switch local identity");
        }

        $roles = array_merge(
            $identity->getRoles(),
            [
                SwitchIdentityRole::fromSource(
                    RoleValue::fromString('ROLE_PREVIOUS_ADMIN'),
                    $currentToken
                )
            ]
        );

        $switchIdentityToken = new GenericLocalToken(
            $identity,
            $identity->getPassword(),
            $this->contextKey,
            $roles
        );

        // dispatch event

        return $switchIdentityToken;
    }

    protected function extractOriginalToken(Tokenable $token): ?Tokenable
    {
        foreach ($token->getRoles() as $role) {
            if ($role instanceof SwitchIdentityRole) {
                return $role->getSource();
            }
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
        return $this->authenticationRequest->match($request)
            && (
                $this->authorizationChecker->isGranted(['ROLE_PREVIOUS_ADMIN'])
                || $this->authorizationChecker->isGranted(['ROLE_USER'])
            );
    }
}