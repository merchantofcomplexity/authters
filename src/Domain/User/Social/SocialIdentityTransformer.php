<?php

namespace MerchantOfComplexity\Authters\Domain\User\Social;

use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class SocialIdentityTransformer
{
    /**
     * @param SocialProviderName $providerName
     * @param SocialiteUser|AbstractUser $user
     * @return GenericSocialIdentity
     */
    public function __invoke(SocialProviderName $providerName, SocialiteUser $user): GenericSocialIdentity
    {
        return new GenericSocialIdentity([
            'identity_provider_id' => $user->getId(),
            'social_provider_name' => $providerName->getValue(),
            'access_token' => $user->token ?? null,
            'secret_token' => $user->tokenSecret ?? null,
            'refresh_token' => $user->refreshToken ?? null,
            'info' => $user->getRaw()
        ]);
    }
}