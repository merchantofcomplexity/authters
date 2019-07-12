<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Exception;

use Illuminate\Contracts\Debug\ExceptionHandler as IlluminateExceptionHandler;

interface DebugFirewall extends IlluminateExceptionHandler
{
    public function setFirewallExceptionHandlerId(string $firewallExceptionHandlerId): void;
}