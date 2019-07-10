<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Recaller;

use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Domain\Identity;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\Recallable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller\RecallerProvider;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Logout;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

abstract class RecallableService implements Recallable, Logout
{
    /**
     * @var ContextKey
     */
    protected $contextKey;

    /**
     * @var QueueingFactory
     */
    private $cookie;

    /**
     * @var RecallerProvider
     */
    protected $recallerProvider;

    /**
     * @var int
     */
    private $hash = 123456789;

    public function __construct(QueueingFactory $cookie,
                                RecallerProvider $recallerProvider,
                                ContextKey $contextKey)
    {
        $this->cookie = $cookie;
        $this->recallerProvider = $recallerProvider;
        $this->contextKey = $contextKey;
    }

    public function autoLogin(Request $request): ?Tokenable
    {
        try {
            if (!$recaller = $this->extractRecaller($request)) {
                return null;
            }

            if (!$this->validateRecaller([$recaller->id(), $recaller->token()])) {
                throw new AuthenticationException("Invalid cookie hash");
            }

            return $this->processAutoLogin($recaller, $request);
        } catch (AuthenticationException $exception) {
            $this->forgetCookie($request);

            return null;
        }
    }

    abstract protected function processAutoLogin(Recaller $recaller, Request $request): Tokenable;

    abstract protected function onLoginSuccess(Request $request, Response $response, Tokenable $token): void;

    public function loginFail(Request $request): void
    {
        $this->forgetCookie($request);
    }

    public function loginSuccess(Request $request, Response $response, Tokenable $token): void
    {
        $this->forgetCookie($request);

        if (!$token->getIdentity() instanceof Identity || !$this->isRememberMeRequested($request)) {
            return;
        }

        $this->onLoginSuccess($request, $response, $token);
    }

    public function logout(Request $request, Tokenable $token, Response $response): void
    {
        $this->forgetCookie($request);
    }

    protected function extractRecaller(Request $request): ?Recaller
    {
        if (!$recaller = $request->cookie($this->cookieName())) {
            return null;
        }

        $recaller = new Recaller($this->decodeRecaller($recaller));

        if ($recaller->valid()) {
            return $recaller;
        }

        throw new AuthenticationException("Invalid authentication via recaller");
    }

    protected function forgetCookie(Request $request): void
    {
        $this->cookie->queue(
            $this->cookie->forget($this->cookieName())
        );
    }

    protected function queueCookie(array $values): void
    {
        $recallerString = $this->encodeRecaller($values);

        $this->cookie->queue(
            $this->cookie->forever($this->cookieName(), $recallerString)
        );
    }

    protected function cookieName(): string
    {
        return '_firewall.recaller.' . $this->contextKey->getValue();
    }

    protected function isRememberMeRequested(Request $request): bool
    {
        return $request->isMethod('post')
            && in_array($request->get('remember-me'), ['true', '1', 'on', 'remember-me'], true);
    }

    private function validateRecaller(array $values): bool
    {
        return hash_equals($this->hash, $this->encodeRecaller($values));
    }

    private function encodeRecaller(array $values): string
    {
        $values = array_merge($values, [$this->hash]);

        $hashed = hash_hmac('sha256', implode('|', $values), $this->hash);

        return base64_encode($hashed);
    }

    private function decodeRecaller(string $recaller): string
    {
        return base64_decode($recaller);
    }
}