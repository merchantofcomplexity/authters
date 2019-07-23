<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Response;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class JWTResponder
{
    public function onFailure(Request $request, ?AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'message' => 'Authentication failure',
            'error' => $exception->getMessage() ?? 'No message'
        ], 403);
    }

    public function entryPoint(Request $request, ?AuthenticationException $exception): Response
    {
        return new JsonResponse([
            'message' => 'authentication required',
            'error' => $exception->getMessage() ?? 'You must login first'
        ], 403);
    }

    public function onSuccess(Request $request, Tokenable $token): Response
    {
        return new JsonResponse([
            'message' => 'Authentication success',
            'token' => $token->getCredentials()->getValue()
        ]);
    }
}