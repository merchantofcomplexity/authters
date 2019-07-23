<?php

namespace MerchantOfComplexity\Authters\Firewall\Context;

use MerchantOfComplexity\Authters\Firewall\Key\DefaultAnonymousKey;
use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\AnonymousKey;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\Key\ContextKey;

trait HasFirewallContext
{
    public function contextKey(): ContextKey
    {
        return new FirewallContextKey($this->context['context_key']);
    }

    public function anonymousKey(): ?AnonymousKey
    {
        if ($this->isAnonymous()) {
            return new DefaultAnonymousKey($this->context['anonymous_key']);
        }

        return null;
    }

    public function isAnonymous(): bool
    {
        return $this->getAttribute('is_anonymous');
    }

    public function isStateless(): bool
    {
        return $this->getAttribute('is_stateless');
    }

    public function canSwitchIdentity(): bool
    {
        return $this->getAttribute('switch_identity', false);
    }


    public function identityProviderId(): ?string
    {
        return $this->getAttribute('identity_provider_id', null);
    }

    public function entryPointId(): string
    {
        return $this->getAttribute('entrypoint_id');
    }

    public function unauthorizedId(): string
    {
        return $this->getAttribute('unauthorized_id');
    }

    public function throttleLogin(): ?array
    {
        return $this->getAttribute('throttle_login', []);
    }

    public function throttleRequest(): ?array
    {
        return $this->getAttribute('throttle_request', []);
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->context[$key]);
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->hasAttribute($key) ? $this->context[$key] : $default;
    }

    public function getAttributes(): array
    {
        return $this->context;
    }
}