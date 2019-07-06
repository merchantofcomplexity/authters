<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class DefaultJsonEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $response;

    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
       $message = $exception->getMessage() ?? ' You must login first';

       return $this->response->json([
           'message' => $message,
           'code' => Response::HTTP_FORBIDDEN,
           'current' => $request->url()
       ], Response::HTTP_FORBIDDEN);
    }
}