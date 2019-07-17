<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class HttpBasicEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var string
     */
    private $realmName;

    public function __construct(ResponseFactory $responseFactory,
                                string $realmName = 'Private access')
    {
        $this->responseFactory = $responseFactory;
        $this->realmName = $realmName;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception = null): Response
    {
        $statusCode = Response::HTTP_UNAUTHORIZED;

        $headers = ['WWW-authenticate' => sprintf('Basic realm="%s"', $this->realmName)];

        return $this->responseFactory->view("errors.$statusCode", [], $statusCode, $headers);
    }
}