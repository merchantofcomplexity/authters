<?php

namespace MerchantOfComplexity\Authters\Exception;

use Exception;
use MerchantOfComplexity\Authters\Support\Contract\Exception\FirewallException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ThrottleRequestsException extends HttpException implements FirewallException
{
    public function __construct(string $message = null,
                                Exception $previous = null,
                                array $headers = [],
                                int $code = 0)
    {
        parent::__construct(429, $message, $previous, $headers, $code);
    }
}