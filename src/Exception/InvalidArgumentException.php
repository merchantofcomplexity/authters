<?php

namespace MerchantOfComplexity\Authters\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Exception\FirewallException;

class InvalidArgumentException extends \InvalidArgumentException implements FirewallException
{
}