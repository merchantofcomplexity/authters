<?php

namespace MerchantOfComplexity\Authters\Firewall\Provision\Local;

use MerchantOfComplexity\Authters\Guard\Authentication\Providers\ProvideLocalAuthentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class LocalAuthProvider extends ProvideLocalAuthentication
{
    protected function createAuthenticatedToken(LocalIdentity $user, Tokenable $token): Tokenable
    {
        return new GenericLocalToken($user, $user->getPassword(), $user->getRoles());
    }
}