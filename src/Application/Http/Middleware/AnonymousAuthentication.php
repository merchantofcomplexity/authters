<?php

namespace MerchantOfComplexity\Authters\Application\Http\Middleware;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Guard\Authentication\Token\GenericAnonymousToken;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class AnonymousAuthentication extends Authentication
{
    /**
     * @var AnonymousKey
     */
    private $anonymousKey;

    public function __construct(AnonymousKey $anonymousKey)
    {
        $this->anonymousKey = $anonymousKey;
    }

    protected function requireAuthentication(Request $request): bool
    {
        return $this->guard->isStorageEmpty();
    }

    protected function processAuthentication(Request $request): ?Response
    {
        try {
            $this->guard->storeAuthenticatedToken(new GenericAnonymousToken($this->anonymousKey));
        } catch (AuthenticationException $exception) {
        }

        return null;
    }
}