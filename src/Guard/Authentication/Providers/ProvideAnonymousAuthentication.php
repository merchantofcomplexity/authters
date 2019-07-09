<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Providers;

use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericAnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AuthenticationProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class ProvideAnonymousAuthentication implements AuthenticationProvider
{
    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(AnonymousKey $anonymousKey)
    {
        $this->anonymousKey = $anonymousKey;
    }

    public function authenticate(Tokenable $token): Tokenable
    {
        return new GenericAnonymousToken($this->anonymousKey);
    }

    public function supportToken(Tokenable $token): bool
    {
        return $token instanceof GenericAnonymousToken
            && $token->getFirewallKey()->sameValueAs($this->anonymousKey);
    }
}