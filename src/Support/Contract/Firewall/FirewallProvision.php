<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Firewall;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\FirewallKey;

interface FirewallProvision
{
    public function firewallKey(): FirewallKey;

    public function serviceId(): string;

    public function callAuthentication(): ?callable;

    public function callProvider(): ?callable;

    public function match(Request $request): bool;
}
