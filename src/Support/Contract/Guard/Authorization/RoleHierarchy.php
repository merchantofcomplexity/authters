<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

interface RoleHierarchy
{
    /**
     * @param string[] $roles
     * @return string[]
     */
    public function getReachableRoles(string ...$roles): array;
}