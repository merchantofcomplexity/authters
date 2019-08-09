<?php

namespace MerchantOfComplexityTest\Authters\Unit\Guard\Authorization\RoleHierarchy;

use MerchantOfComplexity\Authters\Guard\Authorization\Hierarchy\SymfonyRoleHierarchy;
use MerchantOfComplexityTest\Authters\TestCase;

class SymfonyRoleHierarchyTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideRoles
     * @param array $roles
     */
    public function it_build_roles_from_role_hierarchy(array $roles): void
    {
        $reachablesRoles = [
            'ROLE_ADMIN' => [
                'ROLE_MODERATOR',
                'ROLE_USER'
            ],
            'ROLE_MODERATOR' => [
                'ROLE_SUSPEND'
            ],
            'ROLE_USER' => [
                'ROLE_READ'
            ]
        ];

        $hierarchy = new SymfonyRoleHierarchy($reachablesRoles);


        foreach ($roles as $role => $roleHierarchy) {
            $this->assertEquals(
                $hierarchy->getReachableRoles($role), $roleHierarchy
            );
        }
    }

    /**
     * @test
     */
    public function it_return_role_given_for_non_reachable_roles(): void
    {
        $reachablesRoles = [
            'ROLE_ADMIN' => [
                'ROLE_MODERATOR',
                'ROLE_USER'
            ],
        ];

        $hierarchy = new SymfonyRoleHierarchy($reachablesRoles);

        $this->assertEquals(['ROLE_FOO'], $hierarchy->getReachableRoles('ROLE_FOO'));
    }

    public function provideRoles(): iterable
    {
        yield [['ROLE_ADMIN' => ['ROLE_ADMIN', 'ROLE_MODERATOR', 'ROLE_USER', 'ROLE_SUSPEND', 'ROLE_READ']]];
        yield [['ROLE_USER' => ['ROLE_USER', 'ROLE_READ']]];
    }
}