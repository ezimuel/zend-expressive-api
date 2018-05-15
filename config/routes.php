<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Authentication;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Expressive\MiddlewareFactory;

return function (Application $app, MiddlewareFactory $factory, ContainerInterface $container) : void {
    // OAuth2 server route
    $app->route(
        '/oauth',
        Authentication\OAuth2\OAuth2Middleware::class,
        ['GET', 'POST'],
        'oauth'
    );

    // API
    $userRoutePathBase = '/api/users';
    $userRoutePathOptional = $userRoutePathBase . '[/{id}]';
    $userRoutePathFull = $userRoutePathBase . '/{id}';
    $app->get($userRoutePathOptional, App\User\UserHandler::class, 'api.users');
    $app->post($userRoutePathBase, [
        Authentication\AuthenticationMiddleware::class,
        BodyParamsMiddleware::class,
        App\User\CreateUserHandler::class
    ]);
    $app->route(
        $userRoutePathFull,
        [
            Authentication\AuthenticationMiddleware::class,
            BodyParamsMiddleware::class,
            App\User\ModifyUserHandler::class
        ],
        ['PATCH', 'DELETE'],
        'api.user'
    );

    // API docs
    $app->get('/api/doc/invalid-parameter', App\Doc\InvalidParameterHandler::class);
    $app->get('/api/doc/method-not-allowed-error', App\Doc\MethodNotAllowedHandler::class);
    $app->get('/api/doc/resource-not-found', App\Doc\ResourceNotFoundHandler::class);
    $app->get('/api/doc/parameter-out-of-range', App\Doc\OutOfBoundsHandler::class);
    $app->get('/api/doc/runtime-error', App\Doc\RuntimeErrorHandler::class);
};
