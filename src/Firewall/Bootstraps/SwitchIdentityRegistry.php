<?php

namespace MerchantOfComplexity\Authters\Firewall\Bootstraps;

use Closure;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use MerchantOfComplexity\Authters\Application\Http\Middleware\SwitchIdentityAuthentication;
use MerchantOfComplexity\Authters\Application\Http\Request\SwitchIdentityRequest;
use MerchantOfComplexity\Authters\Guard\Authentication\Authenticator\SwitchIdentityAuthenticator;
use MerchantOfComplexity\Authters\Support\Contract\Application\Http\Middleware\Authentication;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallContext;
use MerchantOfComplexity\Authters\Support\Contract\Firewall\FirewallRegistry;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Support\Firewall\FirewallAware;

final class SwitchIdentityRegistry implements FirewallRegistry
{
    public function compose(FirewallAware $firewall, Closure $make)
    {
        if ($firewall->context()->canSwitchIdentity()) {
            $firewall->addPostService('switch_identity', $this->createService());
        }

        return $make($firewall);
    }

    protected function createService(): callable
    {
        return function (Application $app, FirewallContext $context, Request $request): ?Authentication {
            if (!$this->switchIdentityRequest()->match($request)) {
                return null;
            }

            $authenticator = new SwitchIdentityAuthenticator(
                $app->get(AuthorizationChecker::class),
                $app->get($context->identityProviderId()),
                $this->switchIdentityRequest(),
                $app->get(Dispatcher::class),
                $context->contextKey()
            );

            return new SwitchIdentityAuthentication($authenticator, $context->isStateless());
        };
    }

    protected function switchIdentityRequest(): SwitchIdentityRequest
    {
        return new SwitchIdentityRequest();
    }
}