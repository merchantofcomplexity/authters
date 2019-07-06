<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;

final class IdentityLoginFailed
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var AuthenticationException
     */
    private $exception;

    public function __construct(Request $request, AuthenticationException $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function exception(): AuthenticationException
    {
        return $this->exception;
    }
}