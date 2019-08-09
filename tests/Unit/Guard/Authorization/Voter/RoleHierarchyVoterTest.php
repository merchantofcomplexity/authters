<?php

namespace MerchantOfComplexityTest\Authters\Unit\Guard\Authorization\Voter;

use MerchantOfComplexity\Authters\Guard\Authorization\Voter\RoleHierarchyVoter;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\RoleHierarchy;
use MerchantOfComplexityTest\Authters\TestCase;

class RoleHierarchyVoterTest extends TestCase
{
    /**
     * @test
     */
    public function it_grant_role_hierarchy_access(): void
    {
        $roleHierarchy = $this->prophesize(RoleHierarchy::class);
        $token = $this->prophesize(Tokenable::class);

        $token->getRoleNames()->willReturn(['ROLE_FOO', 'ROLE_BAR']);

        $roleHierarchy->getReachableRoles('ROLE_FOO', 'ROLE_BAR')->willReturn(['ROLE_BAR']);

        $voter = new RoleHierarchyVoter($roleHierarchy->reveal());

        $this->assertEquals(1, $voter->vote($token->reveal(), ['ROLE_BAR']));
    }

    /**
     * @test
     */
    public function it_deny_role_hierarchy_access(): void
    {
        $roleHierarchy = $this->prophesize(RoleHierarchy::class);
        $token = $this->prophesize(Tokenable::class);

        $token->getRoleNames()->willReturn(['ROLE_FOO', 'ROLE_BAR']);

        $roleHierarchy->getReachableRoles('ROLE_FOO', 'ROLE_BAR')->willReturn(['ROLE_BABAR']);

        $voter = new RoleHierarchyVoter($roleHierarchy->reveal());

        $this->assertEquals(-1, $voter->vote($token->reveal(), ['ROLE_BAR']));
    }

    /**
     * @test
     */
    public function it_abstain_role_hierarchy_access(): void
    {
        $roleHierarchy = $this->prophesize(RoleHierarchy::class);
        $token = $this->prophesize(Tokenable::class);

        $token->getRoleNames()->willReturn(['ROLE_BAR']);

        $roleHierarchy->getReachableRoles('ROLE_BAR')->willReturn(['ROLE_BAR']);

        $voter = new RoleHierarchyVoter($roleHierarchy->reveal());

        $this->assertEquals(0, $voter->vote($token->reveal(), ['foo']));
    }
}