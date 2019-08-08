<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Contracts\Session\Session;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\ContextEventAware;
use MerchantOfComplexity\Authters\Domain\User\GenericIdentity;
use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexity\Authters\Support\Value\Credentials\BcryptEncodedPassword;
use MerchantOfComplexityTest\Authters\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ContextEventAwareTest extends TestCase
{
    /**
     * @test
     */
    public function it_keep_workflow_if_context_event_has_not_been_dispatched(): void
    {
        $auth = $this->contextEventAwareInstance();

        $this->tokenStorage->getToken()->shouldNotBeCalled();

        $auth->handle($this->request->reveal(), function () {
            return new Response('foo');
        });
    }

    /**
     * @test
     */
    public function it_store_serialized_token_in_session(): void
    {
        $token = $this->tokenInstance();

        $this->tokenStorage->getToken()->willReturn($token);

        $this->trustResolver->isAnonymous($token)->willReturn(false);

        $this->session->put($this->contextEvent->sessionName(), serialize($token))->shouldBeCalled();

        $this->request->session()->willReturn($this->session->reveal());

        $auth = $this->contextEventAwareInstance();

        $auth->handle($this->request->reveal(), function () {
            $this->dispatcher->dispatch($this->contextEvent);

            return new Response('foo');
        });
    }

    public function it_forget_serialized_token_in_session_if_current_token_is_null(): void
    {
        $this->tokenStorage->getToken()->willReturn(null)->shouldBeCalled();

        $this->session->forget($this->contextEvent->sessionName());

        $this->request->session()->willReturn($this->session->reveal());

        $auth = $this->contextEventAwareInstance();

        $auth->handle($this->request->reveal(), function () {
            $this->dispatcher->dispatch($this->contextEvent);

            return new Response('foo');
        });
    }

    /**
     * @test
     */
    public function it_forget_serialized_token_in_session_if_current_token_is_anonymous(): void
    {
        $token = $this->prophesize(Tokenable::class);

        $this->tokenStorage->getToken()->willReturn($token);
        $this->trustResolver->isAnonymous($token)->willReturn(true);
        $this->session->forget($this->contextEvent->sessionName())->shouldBeCalled();
        $this->request->session()->willReturn($this->session->reveal());

        $auth = $this->contextEventAwareInstance();

        $auth->handle($this->request->reveal(), function () {
            $this->dispatcher->dispatch($this->contextEvent);

            return new Response('foo');
        });
    }

    /**
     * @test
     * @expectedException \MerchantOfComplexity\Authters\Exception\RuntimeException
     * @expectedExceptionMessage Context event can run only once per request
     */
    public function it_raise_exception_if_context_event_has_been_dispatched_more_than_once(): void
    {
        $auth = $this->contextEventAwareInstance();

        $auth->handle($this->request->reveal(), function () {
            $this->dispatcher->dispatch($this->contextEvent);

            $this->dispatcher->dispatch($this->contextEvent);

            return new Response('foo');
        });
    }

    protected function tokenInstance(): GenericLocalToken
    {
        $pass = BcryptEncodedPassword::fromString(password_hash('password', 1));

        $id = new GenericIdentity([
            'id' => 'f818ef53-a4a8-413c-961b-239e2d50bdab',
            'password' => $pass->getValue(),
            'roles' => ['ROLE_FOO']
        ]);

        $key = new FirewallContextKey('baz');

        return new GenericLocalToken($id, $pass, $key, $id->getRoles());
    }

    private function contextEventAwareInstance(): ContextEventAware
    {
        return new ContextEventAware(
            $this->tokenStorage->reveal(),
            $this->trustResolver->reveal(),
            $this->dispatcher
        );
    }

    private $tokenStorage;
    private $trustResolver;
    private $dispatcher;
    private $request;
    private $session;
    private $contextEvent;

    protected function setUp()
    {
        $this->tokenStorage = $this->prophesize(TokenStorage::class);
        $this->trustResolver = $this->prophesize(TrustResolver::class);

        $this->session = $this->prophesize(Session::class);
        $this->request = $this->prophesize(Request::class);
        $this->request->setLaravelSession($this->session);

        $key = $this->prophesize(ContextKey::class);
        $key->getValue()->willReturn('key');
        $this->contextEvent = new ContextEvent($key->reveal());

        $this->dispatcher = new Dispatcher();
    }
}