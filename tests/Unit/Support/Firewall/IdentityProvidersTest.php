<?php

namespace MerchantOfComplexityTest\Authters\Unit\Support\Firewall;

use Generator;
use Illuminate\Container\Container;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Support\Contract\Domain\IdentityProvider;
use MerchantOfComplexity\Authters\Support\Firewall\IdentityProviders;
use MerchantOfComplexityTest\Authters\TestCase;

class IdentityProvidersTest extends TestCase
{
    /**
     * @test
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage You must provide at least one identity provider
     */
    public function it_raise_exception_with_empty_identity_providers(): void
    {
        new IdentityProviders();
    }

    /**
     * @test
     */
    public function it_generate_identity_provider(): void
    {
        $container = new Container();

        $container->bind('foo', function () {
            return $this->prophesize(IdentityProvider::class);
        });

        $providers = new IdentityProviders('foo');

        $generator = $providers($container);

        $this->assertInstanceOf(Generator::class, $generator);

        foreach ($generator as $generated){
            $this->assertInstanceOf(IdentityProvider::class, $generated->reveal());
        }
    }
}