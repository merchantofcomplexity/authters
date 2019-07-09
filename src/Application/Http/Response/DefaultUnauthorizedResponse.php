<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AccessDenied;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

final class DefaultUnauthorizedResponse implements AccessDenied
{
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @var string
     */
    private $safePage;

    public function __construct(ResponseFactory $response, string $safePage = '/')
    {
        $this->response = $response;
        $this->safePage = $safePage;
    }

    public function onAuthorizationDenied(Request $request, AuthorizationException $exception): Response
    {
        return $this->response->redirectTo($this->safePage)
            ->with('message', $exception->getMessage());
    }
}