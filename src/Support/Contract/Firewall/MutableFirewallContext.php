<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Firewall;

use MerchantOfComplexity\Authters\Firewall\Context\ImmutableFirewallContext;

interface MutableFirewallContext extends FirewallContext
{
    public function setFirewallContextKey(string $key): void;

    public function setAnonymous(bool $allowAnonymous): void;

    public function setStateless(bool $isStateless): void;

    public function setIdentityProviderId(string $identityProviderId): void;

    public function setEntryPointId(string $entrypointId): void;

    public function setUnauthorizedId(string $unauthorizedId): void;

    public function toImmutable(): ImmutableFirewallContext;

    public function __invoke(array $context): ImmutableFirewallContext;
}