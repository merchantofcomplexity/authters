<?php

namespace MerchantOfComplexity\Authters\Domain\User\Social;

use MerchantOfComplexity\Authters\Support\Contract\Value\Credentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\Value;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class SocialOauthCredentials implements Credentials
{
    /**
     * @var string|null
     */
    private $accessToken;

    /**
     * @var string|null
     */
    private $secretToken;

    /**
     * @var string|null
     */
    private $refreshToken;

    protected function __construct(?string $accessToken,
                                   ?string $secretToken,
                                   ?string $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->secretToken = $secretToken;
        $this->refreshToken = $refreshToken;
    }

    public static function fromString($accessToken = null, $secretToken = null, $refreshToken = null): self
    {
        Assert::nullOrString($accessToken);
        Assert::nullOrString($secretToken);
        Assert::nullOrString($refreshToken);

        return new self($accessToken, $secretToken, $refreshToken);
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getSecretToken(): ?string
    {
        return $this->secretToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getValue(): array
    {
        return [
            'access_token' => $this->accessToken,
            'secret_token' => $this->secretToken,
            'refresh_token' => $this->refreshToken
        ];
    }

    public function sameValueAs(Value $aValue): bool
    {
        return $aValue instanceof $this
            && $this->accessToken === $aValue->getAccessToken()
            && $this->secretToken === $aValue->getSecretToken()
            && $this->refreshToken === $aValue->getRefreshToken();
    }
}