<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;

final class IdentityLogin
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Tokenable
     */
    private $token;

    public function __construct(Request $request, Tokenable $token)
    {
        $this->request = $request;
        $this->token = $token;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }
}