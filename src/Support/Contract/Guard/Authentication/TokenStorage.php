<?php

namespace MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication;

interface TokenStorage
{
    /**
     * Return storage token if exists
     *
     * @return Tokenable|null
     */
    public function getToken(): ?Tokenable;

    /**
     * Set or clear token storage
     *
     * @param Tokenable|null $token
     */
    public function setToken(?Tokenable $token): void;

    /**
     * Check token exists on storage
     *
     * @return bool
     */
    public function hasToken(): bool;

    /**
     * Clear token on storage
     * alias for set token
     *
     * @return bool true if a token pre exits on the storage
     */
    public function clear(): bool;
}