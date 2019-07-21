<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

trait HasConstructorRoles
{
    /**
     * @var string[]
     */
    private $roleNames = [];

    public function __construct(array $roles = [])
    {
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $this->roleNames[] = $role->getRole();
            } elseif (is_string($role)) {
                $this->roleNames[] = $role;
            } else {
                $message = "Role must be a string or implements Role contract " . Role::class;

                throw new AuthenticationServiceFailure($message);
            }
        }
    }

    public function hasRoles(): bool
    {
        return !empty($this->roleNames);
    }

    public function getRoleNames(): array
    {
        return $this->roleNames;
    }
}