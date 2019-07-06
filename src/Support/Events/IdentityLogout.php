<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class IdentityLogout
{
    /**
     * @var Tokenable
     */
    private $token;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Tokenable $token, Request $request)
    {
        $this->token = $token;
        $this->request = $request;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }

    public function request(): Request
    {
        return $this->request;
    }
}