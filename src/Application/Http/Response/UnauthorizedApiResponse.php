<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\AccessDenied;
use MerchantOfComplexity\Authters\Support\Exception\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

final class UnauthorizedApiResponse implements AccessDenied
{
    /**
     * @var ResponseFactory
     */
    private $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function onAuthorizationDenied(Request $request, AuthorizationException $exception): Response
    {
        return $this->response->json([
            'message' => $exception->getMessage(),
            'code' => Response::HTTP_UNAUTHORIZED,
            'current' => $request->url()
        ], Response::HTTP_UNAUTHORIZED);
    }
}