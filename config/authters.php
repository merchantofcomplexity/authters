<?php

use MerchantOfComplexity\Authters\Application\Http\Middleware\Authorization;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AnonymousRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticatableRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticationServiceRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\ContextRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\GuardRegistry;
use MerchantOfComplexity\Authters\Guard\Authorization\Hierarchy\SymfonyRoleHierarchy;
use MerchantOfComplexity\Authters\Guard\Authorization\Strategy\UnanimousAuthorizationStrategy;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\AuthenticatedTokenVoter;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\DefaultExpressionVoter;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\RoleHierarchyVoter;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\RoleVoter;

return
    [
        'identity_providers' => [

        ],

        'authentication' => [

            'group' => [

                'front' => [

                    'context' => [], // array of options or fqcn firewall context

                    'auth' => [
                        'local-login',
                        'local-logout'
                    ]
                ]
            ],

            'bootstraps' => [
                GuardRegistry::class,
                AuthenticatableRegistry::class,
                ContextRegistry::class,
                AuthenticationServiceRegistry::class,
                AnonymousRegistry::class,

            ]
        ],

        'authorization' => [

            'middleware' => Authorization::class,

            'always_authenticate' => false,

            'strategy' => [
                'concrete' => UnanimousAuthorizationStrategy::class,

                'allow_if_all_abstain' => false,

                'voters' => [
                    AuthenticatedTokenVoter::class,
                    RoleHierarchyVoter::class,
                    RoleVoter::class,
                    DefaultExpressionVoter::ALIAS,
                ],
            ],

            'role_hierarchy' => [
                'concrete' => SymfonyRoleHierarchy::class,
                'hierarchy' => [
                    'ROLE_ADMIN' => [
                        'ROLE_USER'
                    ],
                ]
            ]
        ],
    ];