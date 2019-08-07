<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\LocalAuthentication;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericLocalToken;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request\IdentifierCredentialsRequest;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AuthenticationResponse;
use MerchantOfComplexity\Authters\Support\Contract\Domain\RefreshTokenIdentityStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Guardable;
use MerchantOfComplexity\Authters\Support\Contract\Value\ClearCredentials;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use MerchantOfComplexity\Authters\Support\Events\ContextEvent;
use MerchantOfComplexity\Authters\Support\Events\IdentityAttemptLogin;
use MerchantOfComplexity\Authters\Support\Events\IdentityLogin;
use MerchantOfComplexityTest\Authters\TestCase;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;

class LocalAuthenticationTest extends TestCase
{
    use HasNextMiddlewareTest;

    /**
     * @test
     */
    public function it_does_not_authenticate_if_token_storage_is_not_empty(): void
    {
        $this->tokenStorage->getToken()->willReturn($this->token->reveal());
        $this->guard->storage()->willReturn($this->tokenStorage->reveal());

        $this->localAuthenticationInstance()->authenticate(
            $this->request->reveal(),
            $this->nextMiddleware(true)
        );
    }

    /**
     * @test
     */
    public function it_does_not_authenticate_if_request_does_not_match(): void
    {
        $this->tokenStorage->getToken()->willReturn(null);
        $this->guard->storage()->willReturn($this->tokenStorage->reveal());
        $this->credentialsRequest->match($this->request)->willReturn(false);

        $this->localAuthenticationInstance()->authenticate(
            $this->request->reveal(),
            $this->nextMiddleware(true)
        );
    }

    /**
     * @test
     */
    public function it_process_authentication(): void
    {
        $this->tokenStorage->getToken()->willReturn(null);
        $this->guard->storage()->willReturn($this->tokenStorage->reveal());
        $this->credentialsRequest->match($this->request)->willReturn(true);

        //create token
        $id = $this->prophesize(IdentifierValue::class)->reveal();
        $pass = $this->prophesize(ClearCredentials::class)->reveal();
        $this->credentialsRequest->extractIdentifier($this->request)->willReturn($id);
        $this->credentialsRequest->extractPassword($this->request)->willReturn($pass);

        $expectedToken = Argument::type(GenericLocalToken::class);

        $this->guard->fireAuthenticationEvent(Argument::type(IdentityAttemptLogin::class))->shouldBeCalled();

        $token = $this->guard->storeAuthenticatedToken($expectedToken)->willReturn($this->token->reveal());

        $this->guard->fireAuthenticationEvent(Argument::type(IdentityLogin::class))->shouldBeCalled();

        $expectedResponse = new Response('foo');

        // fixMe argument any replace double which are not resolved to exact
        $this->authResponse
            ->onSuccess(Argument::any(), Argument::any())
            ->willReturn($expectedResponse);

        $this->recallable->loginSuccess(Argument::any(), $expectedResponse, Argument::any())->shouldBeCalled();

        $response = $this->localAuthenticationInstance()
            ->authenticate($this->request->reveal(), $this->nextMiddleware(false));

        $this->assertEquals($expectedResponse, $response);
    }

    private function localAuthenticationInstance(): LocalAuthentication
    {
        $auth = new LocalAuthentication(
            $this->credentialsRequest->reveal(),
            $this->authResponse->reveal(),
            $this->contextKey->reveal()
        );

        $auth->setGuard($this->guard->reveal());
        $auth->setRecaller($this->recallable->reveal());

        return $auth;
    }

    private $contextKey;
    private $request;
    private $session;
    private $guard;
    private $tokenStorage;
    private $token;
    private $refreshStrategy;
    private $contextEvent;
    private $recallable;
    private $credentialsRequest;
    private $authResponse;

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
        $this->recallable = $this->prophesize(Recallable::class);
        $this->credentialsRequest = $this->prophesize(IdentifierCredentialsRequest::class);
        $this->authResponse = $this->prophesize(AuthenticationResponse::class);
    }
}