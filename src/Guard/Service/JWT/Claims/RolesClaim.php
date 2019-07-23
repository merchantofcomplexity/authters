<?php

namespace MerchantOfComplexity\Authters\Guard\Service\JWT\Claims;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

class RolesClaim implements Constraint
{
    /**
     * @param Token|Token\Plain $token
     */
    public function assert(Token $token): void
    {
        if(!$token->claims()->get('roles')){
            throw new ConstraintViolation(
                'The token has no role'
            );
        }
    }
}