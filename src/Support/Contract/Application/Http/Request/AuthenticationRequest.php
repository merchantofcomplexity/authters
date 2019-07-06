<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request;

use Illuminate\Http\Request;

interface AuthenticationRequest
{
    public function match(Request $request): bool;

    public function extractCredentials(Request $request);
}