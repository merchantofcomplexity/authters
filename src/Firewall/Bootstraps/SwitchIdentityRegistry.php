<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\SwitchIdentityAuthentication;
use MerchantOfComplexity\Authters\Application\Http\Request\SwitchIdentityAuthenticationRequest;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SwitchIdentityAuthenticator;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class SwitchIdentityRegistry implements FirewallRegistry
{
    /**
     * @var SwitchIdentityAuthenticationRequest
     */
    private $authenticationRequest;

    public function __construct(SwitchIdentityAuthenticationRequest $authenticationRequest)
    {
        $this->authenticationRequest = $authenticationRequest;
    }

    public function compose(FirewallAware $firewall, Closure $make)
    {
        if (true === $firewall->context()->getAttribute('switch_identity')) {
            $service = $this->createSwitchIdentityAuthentication();

            $firewall->addPostService('switch_identity', $service);
        }

        return $make($firewall);
    }

    private function createSwitchIdentityAuthentication(): callable
    {
        return function (Application $app, FirewallContext $context, Request $request): ?Authentication {
            if (!$this->authenticationRequest->match($request)) {
                return null;
            }

            $authenticator = new SwitchIdentityAuthenticator(
                $app->get(AuthorizationChecker::class),
                $app->get($context->identityProviderId()),
                $this->authenticationRequest,
                $context->contextKey()
            );

            return new SwitchIdentityAuthentication($authenticator, $context->isStateless());
        };
    }
}