<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication\Token\Concerns;

use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

trait HasConstructorRoles
{
    /**
     * @var array
     */
    private $roles;

    /**
     * @var string[]
     */
    private $roleNames = [];

    public function __construct(array $roles = [])
    {
        $this->roles = collect($roles)->transform(function ($role) {
            if ($role instanceof Role) {
                $this->roleNames[] = $role->getRole();

                return $role;
            }

            if (is_string($role)) {
                $this->roleNames = $role;

                return RoleValue::fromString($role);
            }

            $message = "Role must be a string or implements Role contract " . Role::class;

            throw new AuthenticationServiceFailure($message);
        })->toArray();
    }

    public function hasRoles(): bool
    {
        return !empty($this->roles);
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getRoleNames(): array
    {
        return $this->roleNames;
    }
}