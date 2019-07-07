<?php

namespace MerchantOfComplexity\Authters\Firewall\Context;

use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Exception\Assert;

final class ImmutableFirewallContext implements FirewallContext
{
    use HasFirewallContext;

    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $FqcnOriginalContext;

    public function __construct(array $context, string $fqcnOriginalContext)
    {
        Assert::notEmpty($context);
        Assert::notBlank($fqcnOriginalContext);

        $this->context = $context;
        $this->FqcnOriginalContext = $fqcnOriginalContext;
    }

    public function fqcnOriginalContext(): string
    {
        return $this->FqcnOriginalContext;
    }
}