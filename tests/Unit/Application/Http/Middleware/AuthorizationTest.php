<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\Authorization;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexityTest\Authters\TestCase;

class AuthorizationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure
     */
    public function it_raise_exception_if_token_storage_is_empty(): void
    {
        $this->storage->getToken()->willReturn(null);

        $auth = $this->authorizationMiddlewareInstance();

        $auth->handle($this->request->reveal(), $this->nextMiddleware(false));
    }

    /**
     * @test
     */
    public function it_does_not_decide_if_attributes_is_empty(): void
    {
        $token = $this->token->reveal();

        $this->storage->getToken()->willReturn($token);
        $this->strategy->decide()->shouldNotBeCalled();

        $auth = $this->authorizationMiddlewareInstance();

        $auth->handle($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_authorize_access(): void
    {
        $token = $this->token->reveal();
        $request = $this->request->reveal();

        $this->storage->getToken()->willReturn($token);

        $this->strategy->decide($token, ['foo'], $request)->willReturn(true);

        $auth = $this->authorizationMiddlewareInstance();

        $auth->handle($request, $this->nextMiddleware(true), 'foo');
    }

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Support\Exception\AuthorizationDenied
     */
    public function it_raise_exception_when_authorization_is_denied(): void
    {
        $token = $this->token->reveal();
        $request = $this->request->reveal();

        $this->storage->getToken()->willReturn($token);

        $this->strategy->decide($token, ['foo'], $request)->willReturn(false);

        $auth = $this->authorizationMiddlewareInstance();

        $auth->handle($request, $this->nextMiddleware(true), 'foo');
    }

    /**
     * @test
     */
    public function it_merge_attributes(): void
    {
        $token = $this->token->reveal();
        $request = $this->request->reveal();

        $this->storage->getToken()->willReturn($token);

        $this->strategy->decide($token, ['bar', 'foo'], $request)->willReturn(true);

        $auth = $this->authorizationMiddlewareInstance(['bar']);

        $auth->handle($request, $this->nextMiddleware(true), 'foo');
    }

    /**
     * @test
     */
    public function it_merge_attributes_from_setter(): void
    {
        $token = $this->token->reveal();
        $request = $this->request->reveal();

        $this->storage->getToken()->willReturn($token);

        $this->strategy->decide($token, ['bar', 'foo_bar', 'foo'], $request)->willReturn(true);

        $auth = $this->authorizationMiddlewareInstance(['bar']);

        $auth->mergeAttributes(['foo_bar']);

        $auth->handle($request, $this->nextMiddleware(true), 'foo');
    }

    private function authorizationMiddlewareInstance(array $defaultAttributes = []): Authorization
    {
        return new Authorization($this->strategy->reveal(), $this->storage->reveal(), $defaultAttributes);
    }

    private $strategy;
    private $storage;
    private $request;
    private $token;

    protected function setUp()
    {
        $this->request = $this->prophesize(Request::class);
        $this->token = $this->prophesize(Tokenable::class);
        $this->strategy = $this->prophesize(AuthorizationStrategy::class);
        $this->storage = $this->prophesize(TokenStorage::class);
    }
}