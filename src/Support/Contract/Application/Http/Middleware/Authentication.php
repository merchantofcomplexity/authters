<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface Authentication
{
    public function authenticate(Request $request): ?Response;
}