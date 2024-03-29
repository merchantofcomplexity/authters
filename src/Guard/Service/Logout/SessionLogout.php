<?php

namespace MerchantOfComplexity\Authters\Guard\Service\Logout;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Logout;
use Symfony\Component\HttpFoundation\Response;

final class SessionLogout implements Logout
{
    public function logout(Request $request, Tokenable $token, Response $response): void
    {
        $request->session()->flush();
    }
}