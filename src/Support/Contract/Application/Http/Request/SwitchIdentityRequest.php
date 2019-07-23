<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Application\Http\Request;

use Illuminate\Http\Request;

interface SwitchIdentityRequest extends AuthenticationRequest
{
    const IDENTIFIER_QUERY = '_switch_identity';

    const EXIT_QUERY = '_exit_identity';

    public function isExitIdentityRequest(Request $request): bool;

    public function isSwitchIdentityRequest(Request $request): bool;
}