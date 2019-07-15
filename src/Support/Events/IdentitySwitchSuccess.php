<?php

namespace MerchantOfComplexity\Authters\Support\Events;

use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Domain\Role\SwitchIdentityRole;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationServiceFailure;

final class IdentitySwitchSuccess
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

        if (!$source = $this->extractSource()) {
            throw new AuthenticationServiceFailure("switch token does not contain the source");
        }
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function token(): Tokenable
    {
        return $this->token;
    }

    public function source(): Tokenable
    {
        return $this->extractSource();
    }

    protected function extractSource(): ?Tokenable
    {
        foreach ($this->token->getRoles() as $role) {
            if ($role instanceof SwitchIdentityRole) {
                return $role->getSource();
            }
        }

        return null;
    }
}