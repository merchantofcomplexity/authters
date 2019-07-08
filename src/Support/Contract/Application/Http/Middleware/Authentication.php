<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface Authentication
{
    public function handle(Request $request): ?Response;
}