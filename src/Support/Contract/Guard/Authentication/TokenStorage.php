<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface TokenStorage
{
    public function getToken(): ?Tokenable;

    public function setToken(?Tokenable $token);

    public function hasToken(): bool;

    public function clear(): bool;
}