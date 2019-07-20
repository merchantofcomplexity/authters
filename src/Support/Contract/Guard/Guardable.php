<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface Guardable
{
    public function authenticateToken(Tokenable $token): Tokenable;

    public function storeAuthenticatedToken(Tokenable $token): Tokenable;

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response;

    /**
     * @param mixed $authenticationEvent
     * @param array $payload
     * @param bool $halt
     * @return array|null
     */
    public function fireAuthenticationEvent($authenticationEvent, array $payload = [], bool $halt = false);

    public function isStorageEmpty(): bool;

    public function isStorageFilled(): bool;

    public function clearStorage(): void;

    public function storage(): TokenStorage;
}