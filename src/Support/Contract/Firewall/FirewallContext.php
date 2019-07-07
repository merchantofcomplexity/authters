<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Firewall;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;

interface FirewallContext
{
    public function contextKey(): ContextKey;

    public function anonymousKey(): ?AnonymousKey;

    public function isAnonymous(): bool;

    public function isStateless(): bool;

    public function identityProviderId(): ?string;

    public function entryPointId(): string;

    public function unauthorizedId(): string;

    public function hasAttribute(string $key): bool;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getAttribute(string $key, $default = null);

    public function getAttributes(): array;
}