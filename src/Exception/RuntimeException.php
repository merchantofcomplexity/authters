<?php

namespace MerchantOfComplexity\Authters\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Exception\FirewallException;

class RuntimeException extends \RuntimeException implements FirewallException
{
}