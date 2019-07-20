<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericRecallerToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityChecker;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\RecallerToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class ProvideRecallerAuthentication implements AuthenticationProvider
{
    /**
     * @var IdentityChecker
     */
    private $identityChecker;

    /**
     * @var ContextKey
     */
    private $contextKey;

    public function __construct(IdentityChecker $identityChecker, ContextKey $contextKey)
    {
        $this->identityChecker = $identityChecker;
        $this->contextKey = $contextKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        $identity = $token->getIdentity();

        $this->identityChecker->onPreAuthentication($identity);

        $newToken = new GenericRecallerToken($identity, $this->contextKey);
        $newToken->setAttributes($token->getAttributes());

        return $newToken;
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof RecallerToken
            && $token->getFirewallKey()->sameValueAs($this->contextKey);
    }
}