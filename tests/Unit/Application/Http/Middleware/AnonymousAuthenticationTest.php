<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\AnonymousAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\AnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;

class AnonymousAuthenticationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     */
    public function it_check_if_authentication_is_required(): void
    {
        $this->guard->isStorageEmpty()->willReturn(false);

        $this->guard->storeAuthenticatedToken()->shouldNotBeCalled();

        $auth = new AnonymousAuthentication($this->key->reveal());

        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $this->guard->isStorageEmpty()->willReturn(true);

        $this->guard->storeAuthenticatedToken(Argument::type(AnonymousToken::class))->shouldbeCalled();

        $auth = new AnonymousAuthentication($this->key->reveal());

        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_keep_request_workflow_on_authentication_exception(): void
    {
        $this->guard->isStorageEmpty()->willReturn(true);

        $this->guard->storeAuthenticatedToken(Argument::type(AnonymousToken::class))->willThrow(
            new AuthenticationException('foo')
        );

        $auth = new AnonymousAuthentication($this->key->reveal());

        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    private $key;
    private $request;
    private $guard;
    private $token;

    protected function setUp(): void
    {
        $this->key = $this->prophesize(AnonymousKey::class);
        $this->request = $this->prophesize(Request::class);
        $this->guard = $this->prophesize(Guardable::class);
        $this->token = $this->prophesize(Tokenable::class);
    }
}