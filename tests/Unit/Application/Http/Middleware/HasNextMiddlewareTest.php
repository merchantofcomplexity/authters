<?php

namespace MerchantOfComplexityTest\Authters\Unit\Application\Http\Middleware;

use Illuminate\Http\Request;

trait HasNextMiddlewareTest
{
    protected function nextMiddleware(bool $assertNextRequest): callable
    {
        return function (Request $request) use ($assertNextRequest) {
            $this->assertTrue($assertNextRequest);

            return function () use ($request) {
                return $request;
            };
        };
    }
}