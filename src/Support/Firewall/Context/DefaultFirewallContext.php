<?php

namespace MerchantOfComplexity\Authters\Support\Firewall\Context;

use MerchantOfComplexity\Authters\Application\Http\Response\DefaultLoginEntrypoint;
use MerchantOfComplexity\Authters\Application\Http\Response\DefaultUnauthorizedResponse;
use MerchantOfComplexity\Authters\Firewall\Context\HasFirewallContext;
use MerchantOfComplexity\Authters\Firewall\Context\HasMutableContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\MutableFirewallContext;

final class DefaultFirewallContext implements MutableFirewallContext
{
    use HasFirewallContext, HasMutableContext;

    /**
     * @var array
     */
    protected $context = [
        'context_key' => 'front',
        'anonymous_key' => 'anonymous_front_key',
        'is_anonymous' => false,
        'is_stateless' => true,
        'identity_provider_id' => null,
        'entrypoint_id' => DefaultLoginEntrypoint::class,
        'unauthorized_id' => DefaultUnauthorizedResponse::class,
    ];

    public function __construct(array $payload = [])
    {
        $this->context = array_merge($this->context, $payload);
    }
}