<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication as BaseAuthentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Value\IdentifierValue;
use Symfony\Component\HttpFoundation\Response;

final class ThrottleRequestPreAuthentication implements BaseAuthentication
{
    use InteractsWithTime;

    /**
     * @var ContextKey
     */
    private $contextKey;

    /**
     * @var RateLimiter
     */
    private $rateLimiter;

    /**
     * @var TokenStorage
     */
    private $storage;

    public function __construct(ContextKey $contextKey, RateLimiter $rateLimiter, TokenStorage $storage)
    {
        $this->contextKey = $contextKey;
        $this->rateLimiter = $rateLimiter;
        $this->storage = $storage;
    }

    public function authenticate(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1)
    {
        $identifier = $this->getIdentifier();

        $key = $this->resolveRequestSignature($request, $identifier);

        $maxAttempts = $this->resolveMaxAttempts($maxAttempts, $identifier);

        if ($this->rateLimiter->tooManyAttempts($key, $maxAttempts)) {
            throw $this->buildException($key, $maxAttempts);
        }

        $this->rateLimiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders($response, $maxAttempts, $this->calculateRemainingAttempts($key, $maxAttempts));
    }

    protected function buildException(string $key, int $maxAttempts)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );

        return new ThrottleRequestsException(
            'Too Many Attempts.', null, $headers
        );
    }

    protected function resolveRequestSignature(Request $request, ?IdentifierValue $identifier): string
    {
        if ($identifier) {
            $identifierValue = $identifier->getValue();

            $value = is_array($identifierValue) ? implode(',', $identifierValue) : $identifierValue;

            return sha1($value);
        }

        return sha1($request->route()->getDomain() . '|' . $request->ip());
    }

    protected function resolveMaxAttempts(int $maxAttempts, ?IdentifierValue $identifier): int
    {
        if (Str::contains($maxAttempts, '|')) {
            $maxAttempts = explode('|', $maxAttempts, 2)[$identifier ? 1 : 0];
        }

        return $maxAttempts;
    }

    protected function addHeaders(Response $response,
                                  int $maxAttempts,
                                  int $remainingAttempts,
                                  int $retryAfter = null): Response
    {
        $headers = $this->getHeaders($maxAttempts, $remainingAttempts, $retryAfter);

        $response->headers->add($headers);

        return $response;
    }

    protected function getHeaders(int $maxAttempts, int $remainingAttempts, int $retryAfter = null)
    {
        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ];

        if (!is_null($retryAfter)) {
            $headers['Retry-After'] = $retryAfter;
            $headers['X-RateLimit-Reset'] = $this->availableAt($retryAfter);
        }

        return $headers;
    }

    protected function calculateRemainingAttempts(string $key, int $maxAttempts, int $retryAfter = null): int
    {
        return is_null($retryAfter) ? $this->rateLimiter->retriesLeft($key, $maxAttempts) : 0;
    }

    protected function getTimeUntilNextRetry($key): int
    {
        return $this->rateLimiter->availableIn($key);
    }

    protected function getIdentifier(): ?IdentifierValue
    {
        if ($token = $this->storage->getToken()) {
            $identity = $token->getIdentity();

            return $identity instanceof IdentifierValue
                ? null : $identity->getIdentifier();
        }

        return null;
    }
}