<?php

namespace MerchantOfComplexity\Authters\Domain\Role;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class SwitchIdentityRole implements Role
{
    const NAME = 'ROLE_PREVIOUS_ADMIN';

    /**
     * @var string
     */
    private $role;

    /**
     * @var Tokenable
     */
    private $token;

    protected function __construct(Tokenable $token)
    {
        $this->role = self::NAME;
        $this->token = $token;
    }

    public static function fromSource(Tokenable $source): self
    {
        return new self(clone $source);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getSource(): Tokenable
    {
        return $this->token;
    }
}