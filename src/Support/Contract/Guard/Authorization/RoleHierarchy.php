<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization;

interface RoleHierarchy
{
    public function getReachableRoles(array $roles): array;
}