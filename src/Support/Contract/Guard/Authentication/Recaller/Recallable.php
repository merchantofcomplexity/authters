<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Recaller;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use Symfony\Component\HttpFoundation\Response;

interface Recallable
{
    public function autoLogin(Request $request): ?Tokenable;

    public function loginFail(Request $request): void;

    public function loginSuccess(Request $request, Response $response, Tokenable $token): void;
}