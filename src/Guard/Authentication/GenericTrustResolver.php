<?php

namespace MerchantOfComplexity\Authters\Guard\Authentication;

use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Tokenable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TrustResolver;

final class GenericTrustResolver implements TrustResolver
{
    /**
     * @var string
     */
    private $anonymousFqcn;

    /**
     * @var string
     */
    private $rememberedFqcn;

    public function __construct(string $anonymousFqcn, string $rememberedFqcn)
    {
        $this->anonymousFqcn = $anonymousFqcn;
        $this->rememberedFqcn = $rememberedFqcn;
    }

    public function isFullyAuthenticated(?Tokenable $token): bool
    {
        if(!$token){
            return false;
        };

        return !$this->isAnonymous($token) && !$this->isRemembered($token);
    }

    public function isAnonymous(?Tokenable $token): bool
    {
        if(!$token){
            return false;
        };

        return $token instanceof $this->anonymousFqcn;
    }

    public function isRemembered(?Tokenable $token): bool
    {
        if(!$token){
            return false;
        };

        return $token instanceof $this->rememberedFqcn;
    }
}