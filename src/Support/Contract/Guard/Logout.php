<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface Logout
{
    public function logout(Tokenable $token, Request $request, Response $response): void;
}