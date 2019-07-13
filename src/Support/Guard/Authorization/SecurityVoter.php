<?php

namespace MerchantOfComplexity\Authters\Support\Guard\Authorization;

use Illuminate\Contracts\Container\Container;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;

final class SecurityVoter
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getIdentity(): ?Identity
    {
        $identity = $this->getStorage()->getToken()->getIdentity();

        if ($identity instanceof Identity) {
            return $identity;
        }

        return null;
    }

    public function getToken(): ?Tokenable
    {
        return $this->getStorage()->getToken();
    }

    public function isGranted(Tokenable $token, array $attributes, object $subject = null)
    {
        return $this->container->get(AuthorizationChecker::class)->isGranted($token, $attributes, $subject);
    }

    public function getStorage(): TokenStorage
    {
        return $this->container->get(TokenStorage::class);
    }
}