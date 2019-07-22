<?php

use MerchantOfComplexity\Authters\Application\Http\Middleware\Authorization;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AnonymousRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticatableRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticationServiceRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\ContextRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\ExceptionRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\GuardRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\RecallerRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\SwitchIdentityRegistry;
use MerchantOfComplexity\Authters\Guard\Authorization\Hierarchy\SymfonyRoleHierarchy;
use MerchantOfComplexity\Authters\Guard\Authorization\Strategy\UnanimousAuthorizationStrategy;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\AuthenticatedTokenVoter;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\DefaultExpressionVoter;
use MerchantOfComplexity\Authters\Guard\Authorization\Voter\RoleHierarchyVoter;

return
    [
        'identity_providers' => [

        ],

        'authentication' => [

            'group' => [

                'front' => [

                    'context' => [], // array of options or fqcn firewall context

                    'auth' => [
                        /**
                         * list of provision
                         */
                    ]
                ]
            ],

            'bootstraps' => [
                GuardRegistry::class,
                AuthenticatableRegistry::class,
                RecallerRegistry::class,
                ContextRegistry::class,
                AuthenticationServiceRegistry::class,
                AnonymousRegistry::class,
                SwitchIdentityRegistry::class,
                ExceptionRegistry::class,
            ]
        ],

        'authorization' => [

            'middleware' => Authorization::class,

            'always_authenticate' => false,

            'strategy' => [
                'concrete' => UnanimousAuthorizationStrategy::class,

                'allow_if_all_abstain' => false,

                'allow_if_equal' => false, // for consensus strategy only

                'voters' => [
                    AuthenticatedTokenVoter::class,
                    RoleHierarchyVoter::class,
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