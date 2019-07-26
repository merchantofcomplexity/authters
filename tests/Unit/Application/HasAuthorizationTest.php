<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application;

use Illuminate\Container\Container;
use MerchantOfComplexity\Authters\Application\HasAuthorization;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;

class HasAuthorizationTest extends TestCase
{
    /**
     * @test
     */
    public function it_check_if_granted(): void
    {
        $this->checker->isGranted(Argument::type('array'), Argument::any())->willReturn(true);
        $this->checker = $this->checker->reveal();

        $instance = $this->getInstance();
        $this->assertTrue($instance->testIsGranted(['foo']));
    }

    /**
     * @test
     * @expectedException MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied
     * @expectedExceptionMessage Authorization denied
     */
    public function it_raise_exception_if_authorization_is_required(): void
    {
        $this->checker->isGranted(Argument::type('array'), Argument::any())->willReturn(false);
        $this->checker = $this->checker->reveal();

        $instance = $this->getInstance();
        $this->assertTrue($instance->testDenyAccessUnlessGranted(['foo']));
    }

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure
     */
    public function it_raise_exception_if_token_is_required_and_token_storage_is_empty(): void
    {
        $this->tokenStorage->getToken()->willReturn(null);
        $this->tokenStorage = $this->tokenStorage->reveal();

        $instance = $this->getInstance();
        $instance->testRequireToken();
    }

    /**
     * @test
     */
    public function it_grant_anonymous(): void
    {
        $this->tokenStorage->getToken()->willReturn($this->token);
        $this->tokenStorage = $this->tokenStorage->reveal();

        $this->trustResolver->isAnonymous(Argument::any())->willReturn(true);
        $this->trustResolver = $this->trustResolver->reveal();

        $instance = $this->getInstance();
        $this->assertTrue($instance->testIsAnonymousIdentity(['foo']));
    }

    /**
     * @test
     */
    public function it_grant_remembered(): void
    {
        $this->tokenStorage->getToken()->willReturn($this->token);
        $this->tokenStorage = $this->tokenStorage->reveal();

        $this->trustResolver->isRemembered(Argument::any())->willReturn(true);
        $this->trustResolver = $this->trustResolver->reveal();

        $instance = $this->getInstance();
        $this->assertTrue($instance->testIsRememberedIdentity(['foo']));
    }

    /**
     * @test
     */
    public function it_grant_fully_authenticated(): void
    {
        $this->tokenStorage->getToken()->willReturn($this->token);
        $this->tokenStorage = $this->tokenStorage->reveal();

        $this->trustResolver->isFullyAuthenticated(Argument::any())->willReturn(true);
        $this->trustResolver = $this->trustResolver->reveal();

        $instance = $this->getInstance();
        $this->assertTrue($instance->testIsFullyAuthenticatedIdentity(['foo']));
    }

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied
     * @expectedException foo_bar
     */
    public function it_raise_authorization_exception(): void
    {
        $instance = $this->getInstance();
        $instance->testRaiseAuthorizationDenied('foo_bar');
    }

    private function getInstance(): object
    {
        $class = new class()
        {
            use HasAuthorization;

            public function testIsGranted($attribute, $subject = null): bool
            {
                return $this->isGranted($attribute);
            }

            public function testDenyAccessUnlessGranted($attribute, $subject = null): bool
            {
                return $this->denyAccessUnlessGranted($attribute, $subject);
            }

            public function testIsAnonymousIdentity(): bool
            {
                return $this->isAnonymousIdentity();
            }

            public function testIsRememberedIdentity(): bool
            {
                return $this->isRememberedIdentity();
            }

            public function testIsFullyAuthenticatedIdentity(): bool
            {
                return $this->isFullyAuthenticatedIdentity();
            }

            public function testRequireToken(): Tokenable
            {
                return $this->requireToken();
            }

            public function testRaiseAuthorizationDenied(string $message): AuthorizationDenied
            {
                return $this->raiseAuthorizationDenied($message);
            }
        };

        $class->setContainer($this->container);

        return $class;
    }

    private $trustResolver;
    private $tokenStorage;
    private $checker;
    private $token;
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->trustResolver = $this->prophesize(TrustResolver::class);
        $this->tokenStorage = $this->prophesize(TokenStorage::class);
        $this->checker = $this->prophesize(AuthorizationChecker::class);
        $this->token = $this->prophesize(Tokenable::class)->reveal();

        $this->container = new Container();

        $this->container->bind(TrustResolver::class, function () {
            return $this->trustResolver;
        });

        $this->container->bind(TokenStorage::class, function () {
            return $this->tokenStorage;
        });

        $this->container->bind(AuthorizationChecker::class, function () {
            return $this->checker;
        });
    }
}