<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Social;

use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\Provider as SocialiteProvider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use MerchantOfComplexity\Authters\Domain\User\Social\SocialProviderName;
use MerchantOfComplexity\Authters\Support\Contract\Domain\SocialIdentity;

final class SocialOAuthFactory
{
    /**
     * @var Factory
     */
    private $socialite;

    /**
     * @var callable
     */
    private $identityTransformer;

    public function __construct(Factory $socialite, callable $identityTransformer)
    {
        $this->socialite = $socialite;
        $this->identityTransformer = $identityTransformer;
    }

    public function socialIdentity(SocialProviderName $providerName): SocialIdentity
    {
        return ($this->identityTransformer)(
            $providerName,
            $this->socialiteUser($providerName)
        );
    }

    public function socialiteInstance(SocialProviderName $providerName): SocialiteProvider
    {
        return $this->socialite->driver($providerName->getValue());
    }

    protected function socialiteUser(SocialProviderName $providerName): SocialiteUser
    {
        return $this->socialiteInstance($providerName)->user();
    }
}