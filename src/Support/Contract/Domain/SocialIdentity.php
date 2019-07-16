<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Domain;

use MerchantOfComplexity\Authters\Domain\User\Social\SocialOauthCredentials;
use MerchantOfComplexity\Authters\Domain\User\Social\SocialOauthIdentifier;

interface SocialIdentity extends Identity
{
    public function getOauthIdentifier(): SocialOauthIdentifier;

    public function getSocialCredentials(): SocialOauthCredentials;

    public function getSocialInfo(): array;
}