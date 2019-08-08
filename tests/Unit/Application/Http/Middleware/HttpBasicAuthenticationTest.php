<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\HttpBasicAuthentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Domain\LocalIdentity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\LocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Events\IdentityAttemptLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLoginFailed;
use MerchantOfComplexity\Authters\Support\Exception\AuthtersValueFailure;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

class HttpBasicAuthenticationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     * @todo make assertions for all others args for methodIsNotAlreadyAuthenticated
     */
    public function it_does_not_require_authentication_if_already_authenticated(): void
    {
        $this->guard->fireAuthenticationEvent()->shouldNotBeCalled();

        $identifier = $this->prophesize(IdentifierValue::class);

        $identifier->sameValueAs($identifier)->willReturn(true);

        $this->credentialsRequest->extractIdentifier($this->request)->willReturn($identifier->reveal());

        $identity = $this->prophesize(LocalIdentity::class);

        $identity->getIdentifier()->willReturn($identifier->reveal());

        $this->localToken->isAuthenticated()->willReturn(true);

        $this->localToken->getIdentity()->willReturn($identity->reveal());

        $this->tokenStorage->getToken()->willReturn($this->localToken->reveal());

        $this->guard->storage()->willReturn($this->tokenStorage->reveal());

        $this->httpBasicAuthenticationInstance()
            ->authenticate(
                $this->request->reveal(),
                $this->nextMiddleware(true)
            );
    }

    /**
     * @test
     */
    public function it_redirect_to_entry_point_on_value_failure(): void
    {
        $this->guard->fireAuthenticationEvent(Argument::type(IdentityLoginFailed::class))->shouldBeCalled();
        $this->guard->clearStorage()->shouldBeCalled();

        $exception = new AuthtersValueFailure('foo', 419, 'bar', 'foo_bar');
        $expectedResponse = new Response('to_entry_point');

        $this->guard->startAuthentication($this->request, $exception)->willReturn($expectedResponse);

        $this->credentialsRequest->extractIdentifier($this->request)->willThrow($exception);

        $response = $this->httpBasicAuthenticationInstance()
            ->authenticate(
                $this->request->reveal(),
                $this->nextMiddleware(false)
            );

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $this->guard->fireAuthenticationEvent(Argument::type(IdentityAttemptLogin::class))->shouldBeCalled();
        $this->guard->fireAuthenticationEvent(Argument::type(IdentityLogin::class))->shouldBeCalled();

        $this->guard->fireAuthenticationEvent()->shouldNotBeCalled();

        $identifier = $this->prophesize(IdentifierValue::class);
        $password = $this->prophesize(ClearCredentials::class);

        //failed point
        $identifier->sameValueAs($identifier)->willReturn(false);

        $this->credentialsRequest->extractIdentifier($this->request)->willReturn($identifier->reveal());
        $this->credentialsRequest->extractPassword($this->request)->willReturn($password->reveal());

        $identity = $this->prophesize(LocalIdentity::class);

        $identity->getIdentifier()->willReturn($identifier->reveal());

        $this->localToken->isAuthenticated()->willReturn(true);

        $this->localToken->getIdentity()->willReturn($identity->reveal());

        $this->tokenStorage->getToken()->willReturn($this->localToken->reveal());

        $this->guard->storage()->willReturn($this->tokenStorage->reveal());

        $this->guard->storeAuthenticatedToken(Argument::type(GenericLocalToken::class))->willReturn(
            $this->localToken->reveal()
        );

        $this->httpBasicAuthenticationInstance()
            ->authenticate(
                $this->request->reveal(),
                $this->nextMiddleware(true)
            );
    }

    private function httpBasicAuthenticationInstance(): HttpBasicAuthentication
    {
        $auth = new HttpBasicAuthentication(
            $this->credentialsRequest->reveal(),
            $this->contextKey->reveal()
        );

        $auth->setGuard($this->guard->reveal());

        return $auth;
    }

    private $credentialsRequest;
    private $contextKey;
    private $token;
    private $localToken;
    private $tokenStorage;
    private $request;
    private $guard;

    protected function setUp()
    {
        $this->credentialsRequest = $this->prophesize(IdentifierCredentialsRequest::class);
        $this->contextKey = $this->prophesize(ContextKey::class);
        $this->token = $this->prophesize(Tokenable::class);
        $this->localToken = $this->prophesize(LocalToken::class);
        $this->tokenStorage = $this->prophesize(TokenStorage::class);
        $this->request = $this->prophesize(Request::class);

        $this->guard = $this->prophesize(Guardable::class);
    }
}