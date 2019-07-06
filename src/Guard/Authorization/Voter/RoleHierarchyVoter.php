<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Voter;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\RoleHierarchy;

final class RoleHierarchyVoter extends RoleVoter
{
    /**
     * @var RoleHierarchy
     */
    private $roleHierarchy;

    public function __construct(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }

    protected function extractRoles(Tokenable $token): array
    {
        return $this->roleHierarchy->getReachableRoles($token->getRoles());
    }
}