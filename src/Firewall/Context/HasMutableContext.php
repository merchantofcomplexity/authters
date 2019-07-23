<?php

namespace MerchantOfComplexity\Authters\Firewall\Context;

use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Firewall\Key\FirewallContextKey;
use function get_class;

trait HasMutableContext
{
    public function setFirewallContextKey(string $key): void
    {
        $this->setAttribute('context_key', new FirewallContextKey($key));
    }

    public function setAnonymousKey(string $key): void
    {
        $this->setAttribute('anonymous_key', $key);
    }

    public function setAnonymous(bool $allowAnonymous): void
    {
        $this->setAttribute('is_anonymous', $allowAnonymous);
    }

    public function setStateless(bool $isStateless): void
    {
        $this->setAttribute('is_stateless', $isStateless);
    }

    public function enableSwitchIdentity(bool $enable): void
    {
        $this->setAttribute('switch_identity', $enable);
    }

    public function setIdentityProviderId(string $identityProviderId): void
    {
        $this->setAttribute('identity_provider_id', $identityProviderId);
    }

    public function setEntryPointId(string $entrypointId): void
    {
        $this->setAttribute('entrypoint_id', $entrypointId);
    }

    public function setUnauthorizedId(string $unauthorizedId): void
    {
        $this->setAttribute('unauthorized_id', $unauthorizedId);
    }

    public function setThrottleLogin(array $throttleLogin): void
    {
        if (!isset($throttleLogin['request'])) {
            throw new InvalidArgumentException("Missing key request for context throttle login");
        }

        $this->setAttribute('throttle_login', $throttleLogin);
    }

    public function setThrottleRequest(array $throttleLogin): void
    {
        $this->setAttribute('throttle_request', $throttleLogin);
    }

    public function toImmutable(): ImmutableFirewallContext
    {
        return new ImmutableFirewallContext($this->context, get_class($this));
    }

    public function __invoke(array $context): ImmutableFirewallContext
    {
        foreach ($context as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this->toImmutable();
    }

    private function setAttribute(string $key, $value): void
    {
        $this->context[$key] = $value;
    }
}