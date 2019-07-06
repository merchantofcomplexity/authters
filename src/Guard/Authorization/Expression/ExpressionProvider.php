<?php

namespace MerchantOfComplexity\Authters\Guard\Authorization\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

final class ExpressionProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('is_anonymous', function () {
                return '$trust_resolver->isAnonymous($token)';
            }, function (array $variables) {
                return $variables['trust_resolver']->isAnonymous($variables['token']);
            }),

            new ExpressionFunction('is_fully_authenticated', function () {
                return '$trust_resolver->isFullyAuthenticated($token)';
            }, function (array $variables) {
                return $variables['trust_resolver']->isFullyAuthenticated($variables['token']);
            }),

            new ExpressionFunction('is_remember_me', function () {
                return '$trust_resolver->isRemembered($token)';
            }, function (array $variables) {
                return $variables['trust_resolver']->isRemembered($variables['token']);
            }),

            new ExpressionFunction('is_authenticated', function () {
                return '$token && !$trust_resolver->isAnonymous($token)';
            }, function (array $variables) {
                return $variables['token'] && !$variables['trust_resolver']->isAnonymous($variables['token']);
            }),

            new ExpressionFunction('has_role', function ($role) {
                return sprintf('in_array(%s, $roles)', $role);
            }, function (array $variables, $role) {
                return in_array($role, $variables['roles']);
            }),
        ];
    }
}