<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

interface AuthenticationSuccess
{
    public function onAuthenticationSuccess(Request $request, Tokenable $token): Response;
}