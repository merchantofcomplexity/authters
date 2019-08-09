<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface Guardable
{
    /**
     * Send the token to be authenticated
     *
     * @param Tokenable $token
     * @return Tokenable
     */
    public function authenticateToken(Tokenable $token): Tokenable;

    /**
     * Send and store an authenticated token
     *
     * @param Tokenable $token
     * @return Tokenable
     */
    public function storeAuthenticatedToken(Tokenable $token): Tokenable;

    /**
     * Return to an entry point on authentication failure
     *
     * @param Request $request
     * @param AuthenticationException|null $exception
     * @return Response
     */
    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response;

    /**
     * @param mixed $authenticationEvent
     * @param array $payload
     * @param bool $halt
     * @return array|null
     */
    public function fireAuthenticationEvent($authenticationEvent, array $payload = [], bool $halt = false);

    /**
     * Check if the token storage is empty
     *
     * @return bool
     */
    public function isStorageEmpty(): bool;

    /**
     * Check if the token storage has any token
     *
     * @return bool
     */
    public function isStorageFilled(): bool;

    /**
     * Clear the token storage
     */
    public function clearStorage(): void;

    /**
     * Access to the token storage
     *
     * @return TokenStorage
     */
    public function storage(): TokenStorage;
}