<?php

return [

    'authentication' => [

        'identity_providers' => [

        ],

        'firewall' => [
            'front' => [
                'middleware' => [
                    \MerchantOfComplexity\Authters\Application\Http\Middleware\ContextAuthentication::class,
                    'local-login'
                ]
            ],

            'api' => [

            ]
        ],

    ],

    'authorization' => [

    ]
];