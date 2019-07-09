<?php

use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticatableRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\AuthenticationServiceRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\ContextRegistry;
use MerchantOfComplexity\Authters\Firewall\Bootstraps\GuardRegistry;

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
                AuthenticationServiceRegistry::class
            ]
        ],

        'authorization' => [

        ],
    ];