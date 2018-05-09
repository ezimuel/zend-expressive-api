<?php
declare(strict_types=1);

namespace App;

use Zend\Expressive\Authentication;
use Zend\Expressive\Hal\Metadata\MetadataMap;
use Zend\Expressive\Hal\Metadata\RouteBasedCollectionMetadata;
use Zend\Expressive\Hal\Metadata\RouteBasedResourceMetadata;
use Zend\Hydrator\ObjectProperty as ObjectPropertyHydrator;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            MetadataMap::class => $this->getHalConfig(),
            'authentication' => $this->getAuthenticationConfig()
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies() : array
    {
        return [
            'factories'  => [
                User\CreateUserHandler::class => User\CreateUserHandlerFactory::class,
                User\ModifyUserHandler::class => User\ModifyUserHandlerFactory::class,
                User\UserHandler::class => User\UserHandlerFactory::class,
                User\UserModel::class => User\UserModelFactory::class
            ],
            'aliases' => [
                Authentication\AuthenticationInterface::class => Authentication\OAuth2\OAuth2Adapter::class,
            ],
        ];
    }

    public function getHalConfig() : array
    {
        return [
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => User\UserEntity::class,
                'route' => 'api.user',
                'extractor' => ObjectPropertyHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' =>  User\UserCollection::class,
                'collection_relation' => 'users',
                'route' => 'api.users',
            ]
        ];
    }

    public function getAuthenticationConfig()
    {
        return [
            'private_key'    => __DIR__ . '/../../data/oauth2/private.key',
            'public_key'     => __DIR__ . '/../../data/oauth2/public.key',
            'encryption_key' => require __DIR__ . '/../../data/oauth2/encryption.key',
            'access_token_expire'  => 'P1D',
            'refresh_token_expire' => 'P1M',
            'auth_code_expire'     => 'PT10M',
            'pdo' => [
                'dsn'      => 'sqlite:' . __DIR__ . '/../../data/oauth2.sqlite'
            ]
        ];
    }
}
