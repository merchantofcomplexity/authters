<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface Logout
{
    /**
     * Logout the request
     *
     * @param Request $request
     * @param Tokenable $token
     * @param Response $response
     */
    public function logout(Request $request, Tokenable $token, Response $response): void;
}