<?php

namespace MerchantOfComplexity\Authters\Domain\Role;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class SwitchIdentityRole implements Role
{
    /**
     * @var RoleValue
     */
    private $role;

    /**
     * @var Tokenable
     */
    private $token;

    protected function __construct(RoleValue $role, Tokenable $token)
    {
        $this->role = $role;
        $this->token = $token;
    }

    public static function fromSource(RoleValue $role, Tokenable $source): self
    {
        return new self(clone $role, clone $source);
    }

    public function getRole(): string
    {
        return $this->role->getRole();
    }

    public function getSource(): Tokenable
    {
        return $this->token;
    }
}