<?php

namespace MerchantOfComplexity\Authters\Support\Firewall\Context;

use MerchantOfComplexity\Authters\Application\Http\Response\DefaultJsonEntrypoint;
use MerchantOfComplexity\Authters\Application\Http\Response\DefaultUnauthorizedResponse;
use MerchantOfComplexity\Authters\Firewall\Context\HasFirewallContext;
use MerchantOfComplexity\Authters\Firewall\Context\HasMutableContext;
use MerchantOfComplexity\Authters\Guard\Service\JWT\Http\Request\ApiLoginRequest;
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
        'switch_identity' => false,
        'identity_provider_id' => null,
        'entrypoint_id' => DefaultJsonEntrypoint::class,
        'unauthorized_id' => DefaultUnauthorizedResponse::class,
        'throttle_login' => [
            'request' => ApiLoginRequest::class,
            'decay' => 1,
            'max_attempts' => 5
        ],
        'throttle_request' => [
            'decay' => 1,
            'max_attempts' => 60
        ]
    ];

    public function __construct(array $payload = [])
    {
        $this->context = array_merge($this->context, $payload);
    }
}