<?php

namespace MerchantOfComplexity\Authters\Firewall\Context;

use MerchantOfComplexity\Authters\Application\Http\Response\DefaultJsonEntrypoint;
use MerchantOfComplexity\Authters\Application\Http\Response\DefaultUnauthorizedResponse;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\MutableFirewallContext;

final class ApiFirewallContext implements MutableFirewallContext
{
    use HasFirewallContext, HasMutableContext;

    /**
     * @var array
     */
    protected $context = [
        'context_key' => 'api',
        'anonymous_key' => 'anonymous_api_key',
        'is_anonymous' => true,
        'is_stateless' => true,
        'identity_provider_id' => null,
        'entrypoint_id' => DefaultJsonEntrypoint::class,
        'unauthorized_id' => DefaultUnauthorizedResponse::class,
    ];

    public function __construct(array $payload = [])
    {
        $this->context = array_merge($this->context, $payload);
    }
}