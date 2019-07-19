<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface Authentication
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return Closure|Response
     */
    public function authenticate(Request $request, Closure $next);
}