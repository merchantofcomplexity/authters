<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextAuthentication;
use MerchantOfComplexity\Authters\Domain\User\GenericIdentity;
use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Domain\RefreshTokenIdentityStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;
use Ramsey\Uuid\Uuid;

class ContextAuthenticationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     */
    public function it_dispatch_context_event_on_authentication_required(): void
    {
        $this->guard->fireAuthenticationEvent($this->contextEvent)->shouldBeCalled();

        $this->session->has('_firewall_foo')->willReturn(false);
        $this->request->session()->willReturn($this->session->reveal());

        $auth = $this->contextAuthenticationInstance();
        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_keep_request_workflow_on_authentication_exception(): void
    {
        $this->guard->fireAuthenticationEvent($this->contextEvent)->shouldBeCalled();
        $this->guard->clearStorage()->shouldBeCalled();

        $this->session->has('_firewall_foo')->willReturn(true);
        $this->session->get($this->contextEvent->sessionName())->willReturn(serialize($this->tokenInstance()));
        $this->request->session()->willReturn($this->session->reveal());

        $this->refreshStrategy->refreshTokenIdentity(Argument::type(GenericLocalToken::class))
            ->willThrow(new AuthenticationException('baz'));
        $this->refreshStrategy->reveal();

        $auth = $this->contextAuthenticationInstance();
        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $this->guard->fireAuthenticationEvent($this->contextEvent)->shouldBeCalled();

        $this->session->has('_firewall_foo')->willReturn(true);
        $this->session->get($this->contextEvent->sessionName())->willReturn(serialize($this->tokenInstance()));
        $this->request->session()->willReturn($this->session->reveal());

        $this->refreshStrategy->refreshTokenIdentity(Argument::type(GenericLocalToken::class))
            ->willReturn($this->token);

        $this->tokenStorage->setToken($this->token->reveal())->shouldBeCalled();
        $this->guard->storage()->willReturn($this->tokenStorage);

        $auth = $this->contextAuthenticationInstance();
        $auth->setGuard($this->guard->reveal());

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    private function contextAuthenticationInstance(): ContextAuthentication
    {
        return new ContextAuthentication($this->contextEvent, $this->refreshStrategy->reveal());
    }

    protected function tokenInstance(): GenericLocalToken
    {
        $pass = BcryptEncodedPassword::fromString(password_hash('password', 1));

        $id = new GenericIdentity([
            'id' => Uuid::uuid4()->toString(),
            'password' => $pass->getValue(),
            'roles' => ['ROLE_FOO']
        ]);

        $key = new FirewallContextKey('baz');

        return new GenericLocalToken($id, $pass, $key, $id->getRoles());
    }

    private $contextKey;
    private $request;
    private $session;
    private $guard;
    private $tokenStorage;
    private $token;
    private $refreshStrategy;
    private $contextEvent;

    protected function setUp(): void
    {
        $this->request = $this->prophesize(Request::class);
        $this->session = $this->prophesize(Session::class);
        $this->guard = $this->prophesize(Guardable::class);
        $this->tokenStorage = $this->prophesize(TokenStorage::class);
        $this->token = $this->prophesize(Tokenable::class);
        $this->refreshStrategy = $this->prophesize(RefreshTokenIdentityStrategy::class);
        $this->contextKey = $this->prophesize(ContextKey::class);
        $this->contextKey->getValue()->willReturn('foo');
        $this->contextEvent = new ContextEvent($this->contextKey->reveal());
    }
}