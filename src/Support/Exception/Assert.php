<?php

namespace MerchantOfComplexity\Authters\Support\Exception;

use Assert\Assertion;

final class Assert extends Assertion
{
    /**
     * @var string
     */
    protected static $exceptionClass = AuthtersValueFailure::class;
}