<?php

return [

    'authentication' => [

        'identity_providers' => [

        ],

        'firewall' => [
            'front' => [
                'middleware' => [
                    \MerchantOfComplexity\Authters\Application\Http\Middleware\ContextAuthenticationMiddleware::class,
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