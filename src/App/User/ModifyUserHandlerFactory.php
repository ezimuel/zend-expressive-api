<?php
namespace App\User;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;

class ModifyUserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ModifyUserHandler
    {
        $filters = $container->get('InputFilterManager');
        
        return new ModifyUserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $filters->get(UserInputFilter::class)
        );
    }
}
