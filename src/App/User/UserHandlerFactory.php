<?php
namespace App\User;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class UserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserHandler
    {
        return new UserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class)
        );
    }
}
