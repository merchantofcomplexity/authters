<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface AuthenticationResponse
{
    public function entrypoint(Request $request,AuthenticationException $exception = null): Response;

    public function onSuccess(Request $request, Tokenable $token): Response;

    public function onFailure(Request $request, AuthenticationException $exception): Response;
}