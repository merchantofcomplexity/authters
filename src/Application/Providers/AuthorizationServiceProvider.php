<?php

namespace MerchantOfComplexity\Authters\Application\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use MerchantOfComplexity\Authters\Exception\InvalidArgumentException;
use MerchantOfComplexity\Authters\Firewall\Manager;
use MerchantOfComplexity\Authters\Guard\Authorization\AuthorizationChecker;
use MerchantOfComplexity\Authters\Guard\Authorization\Expression\ExpressionLanguage;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\DefaultExpressionVoter;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\Authenticatable;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authentication\TokenStorage;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationChecker as BaseAuthorization;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\AuthorizationStrategy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\RoleHierarchy;
use MerchantOfComplexity\Authters\Support\Contract\Guard\Authorization\Votable;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot(Router $router, Manager $manager, Repository $config): void
    {
       // $authorizationMiddleware = $config->get('authters.authorization.middleware');
        //$this->app->bind($authorizationMiddleware); // singleton for access map

        /*
        foreach ($router->getMiddlewareGroups() as $group) {
            if ($manager->hasFirewall($group)) {
                //$router->pushMiddlewareToGroup()
            }
        }*/
    }

    public function register(): void
    {
        $config = $this->app->get('config')->get('authters.authorization');

        $this->registerAuthorizationStrategy($config);

        $this->registerAuthorizationChecker($config['always_authenticate'] ?? true);

        $this->registerRoleHierarchy($config['role_hierarchy'] ?? []);
    }

    protected function registerAuthorizationStrategy(array $authorization): void
    {
        $this->app->bind(AuthorizationStrategy::class,
            function () use ($authorization): AuthorizationStrategy {
                $concrete = $authorization['strategy']['concrete'];
                $allowIfAllAbstain = $authorization['strategy']['allow_if_all_abstain'] ?? false;
                $voters = $this->resolveVoters($authorization['strategy']['voters'] ?? []);

                return new $concrete($allowIfAllAbstain, ...$voters);
            });
    }

    protected function registerAuthorizationChecker(bool $alwaysAuthenticate): void
    {
        $this->app->bind(BaseAuthorization::class,
            function (Application $app) use ($alwaysAuthenticate): BaseAuthorization {
                return new AuthorizationChecker(
                    $app->make(Authenticatable::class),
                    $app->make(AuthorizationStrategy::class),
                    $app->make(TokenStorage::class),
                    $alwaysAuthenticate
                );
            });
    }

    protected function registerRoleHierarchy(array $roleHierarchy): void
    {
        if ($roleHierarchy && !$this->app->bound(RoleHierarchy::class)) {
            $this->app->bind(RoleHierarchy::class, function () use ($roleHierarchy) {
                return new $roleHierarchy['concrete']($roleHierarchy['hierarchy']);
            });
        }
    }

    protected function resolveVoters(array $voters): array
    {
        if (!$voters) {
            throw new InvalidArgumentException("You must add at least on voter in configuration");
        }

        if (in_array(DefaultExpressionVoter::ALIAS, $voters)) {
            $this->app->bind(ExpressionLanguage::class);

            $this->app->bind(DefaultExpressionVoter::ALIAS, DefaultExpressionVoter::class);
        }

        return collect($voters)->transform(function (string $voter): Votable {
            return $this->app->make($voter);
        })->toArray();
    }

    public function provides(): array
    {
        return [BaseAuthorization::class, AuthorizationStrategy::class, RoleHierarchy::class];
    }
}