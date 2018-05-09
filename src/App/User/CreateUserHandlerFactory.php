<?php
namespace App\User;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Helper\UrlHelper;

class CreateUserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : CreateUserHandler
    {
        $filters = $container->get('InputFilterManager');
        
        return new CreateUserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(UrlHelper::class),
            $filters->get(UserInputFilter::class)
        );
    }
}
