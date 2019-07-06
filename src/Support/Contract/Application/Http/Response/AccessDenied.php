<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

interface AccessDenied
{
    public function onAuthorizationDenied(Request $request, AuthenticationException $exception): Response;
}