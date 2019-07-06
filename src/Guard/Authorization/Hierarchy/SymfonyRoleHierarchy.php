<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Hierarchy;

use MerchantOfComplexity\Authters\Domain\Role\RoleValue;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Role;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\RoleHierarchy;

final class SymfonyRoleHierarchy implements RoleHierarchy
{
    /**
     * @var array
     */
    private $map;

    /**
     * @var array
     */
    private $rolesHierarchy;

    public function __construct(array $rolesHierarchy)
    {
        $this->rolesHierarchy = $rolesHierarchy;
        $this->buildRoleMap();
    }

    protected function buildRoleMap(): void
    {
        $this->map = [];

        foreach ($this->rolesHierarchy as $main => $roles) {
            $this->map[$main] = $roles;
            $visited = [];
            $additionalRoles = $roles;

            while ($role = array_shift($additionalRoles)) {
                if (!array_key_exists($role, $this->rolesHierarchy)) {
                    continue;
                }

                $visited[] = $role;

                $this->map[$main] = array_unique(array_merge($this->map[$main], $this->rolesHierarchy[$role]));

                $additionalRoles = array_merge($additionalRoles, array_diff($this->rolesHierarchy[$role], $visited));
            }
        }
    }

    public function getReachableRoles(array $roles): array
    {
        $reachableRoles = $roles;

        /** @var Role $role */
        foreach ($roles as $role) {
            $role = $role->getRole();

            if (!array_key_exists($role, $this->map)) {
                continue;
            }

            foreach ((array)$this->map[$role] as $r) {
                $reachableRoles[] = RoleValue::fromString($r);
            }
        }

        return $reachableRoles;
    }
}