<?php

namespace MerchantOfComplexity\Authters\Application\Http\Response;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Response\Entrypoint;
use MerchantOfComplexity\Authters\Support\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

final class DefaultHomeEntrypoint implements Entrypoint
{
    /**
     * @var ResponseFactory
     */
    private $response;

    /**
     * @var string
     */
    private $home;

    public function __construct(ResponseFactory $response, string $home = '/')
    {
        $this->response = $response;
        $this->home = $home;
    }

    public function startAuthentication(Request $request, AuthenticationException $exception): Response
    {
        return $this->response->redirectTo($this->home)
            ->with('message', $exception->getMessage());
    }
}