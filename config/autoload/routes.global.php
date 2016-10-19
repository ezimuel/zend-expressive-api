<?php

return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => Zend\Expressive\Router\FastRouteRouter::class,
            App\Middleware\Ping::class => App\Middleware\Ping::class,
        ],
        'factories' => [
            App\Middleware\User::class => App\Middleware\UserFactory::class
        ],
    ],
];
