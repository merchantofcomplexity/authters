<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Exception\RuntimeException;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use MerchantOfComplexityTest\Authters\TestCase;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     */
    public function it_keep_workflow_when_authentication_is_not_required(): void
    {
        $auth = new class() extends Authentication
        {
            protected function requireAuthentication(Request $request): bool
            {
                return false;
            }

            protected function processAuthentication(Request $request): ?Response
            {
                throw new RuntimeException("should not be called");
            }
        };

        $auth->authenticate($this->request->reveal(), $this->nextMiddleware(true));
    }

    /**
     * @test
     */
    public function it_return_response_on_authentication(): void
    {
        $response = $this->response;

        $auth = new class($response) extends Authentication
        {
            private $response;

            public function __construct($response)
            {
                $this->response = $response;
            }

            protected function requireAuthentication(Request $request): bool
            {
                return true;
            }

            protected function processAuthentication(Request $request): ?Response
            {
                return $this->response;
            }
        };

        $response = $auth->authenticate($this->request->reveal(), $this->nextMiddleware(false));

        $this->assertEquals($this->response, $response);
    }

    /**
     * @test
     */
    public function it_start_authentication_on_authentication_exception(): void
    {
        $exception = $this->exception;

        $auth = new class($exception) extends Authentication
        {
            private $exception;

            public function __construct($exception)
            {
                $this->exception = $exception;
            }

            protected function requireAuthentication(Request $request): bool
            {
                return true;
            }

            protected function processAuthentication(Request $request): ?Response
            {
                throw $this->exception;
            }
        };

        $expectedResponse = new Response('baz');

        $this->guard->startAuthentication($this->request, $this->exception)->willReturn($expectedResponse);

        $auth->setGuard($this->guard->reveal());

        $response = $auth->authenticate($this->request->reveal(), $this->nextMiddleware(false));

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function it_dispatch_failure_login_event_on_authentication_exception(): void
    {
        $exception = $this->exception;

        $auth = new class($exception) extends Authentication
        {
            private $exception;

            public function __construct($exception)
            {
                $this->exception = $exception;
            }

            protected function requireAuthentication(Request $request): bool
            {
                return true;
            }

            protected function processAuthentication(Request $request): ?Response
            {
                throw $this->exception;
            }

            protected function fireFailureLoginEvent(Request $request, AuthenticationException $exception): void
            {
                $this->guard->fireAuthenticationEvent('foo');
            }
        };

        $expectedResponse = new Response('baz');

        $this->guard->fireAuthenticationEvent('foo')->shouldBeCalled();

        $this->guard->startAuthentication($this->request, $this->exception)->willReturn($expectedResponse);

        $auth->setGuard($this->guard->reveal());

        $response = $auth->authenticate($this->request->reveal(), $this->nextMiddleware(false));

        $this->assertEquals($expectedResponse, $response);
    }

    private $guard;
    private $request;
    private $response;
    private $exception;

    protected function setUp(): void
    {
        $this->guard = $this->prophesize(Guardable::class);
        $this->request = $this->prophesize(Request::class);
        $this->response = $this->prophesize(Response::class)->reveal();
        $this->exception = new AuthenticationException('foo');
    }
}