<?php

namespace MerchantOfComplexity\Authters\Exception;

use MerchantOfComplexity\Authters\Support\Contract\Exception\AuthtersException;

class InvalidArgumentException extends \InvalidArgumentException implements AuthtersException
{

}