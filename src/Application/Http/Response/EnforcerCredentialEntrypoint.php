<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class EnforcerCredentialEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var string
     */
    private $routeName;

    public function __construct(ResponseFactory $responseFactory, string $routeName)
    {
        $this->responseFactory = $responseFactory;
        $this->routeName = $routeName;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        return $this->responseFactory
            ->redirectToRoute($this->routeName)
            ->with('message', $exception ? $exception->getMessage() : 'Confirm your identity');
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }
}