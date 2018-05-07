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
    $app->get('/api/users[/{id}]', App\Handler\UserHandler::class, 'api.users');
    $app->route(
        '/api/users[/{id}]',
        [
            Authentication\AuthenticationMiddleware::class,
            BodyParamsMiddleware::class,
            App\Handler\UserHandler::class
        ],
        ['POST', 'PATCH', 'DELETE']
    );
};
