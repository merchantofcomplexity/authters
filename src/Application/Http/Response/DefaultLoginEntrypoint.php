<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class DefaultLoginEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @var string
     */
    private $loginUrl;

    public function __construct(ResponseFactory $response, string $loginUrl = '/auth/login')
    {
        $this->response = $response;
        $this->loginUrl = $loginUrl;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
        return $this->response->redirectTo($this->loginUrl)
            ->with('message', $exception->getMessage());
    }
}