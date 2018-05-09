<?php
namespace App\User;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Helper\UrlHelper;

class UserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserHandler
    {
        $filters = $container->get('InputFilterManager');
        
        return new UserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(UrlHelper::class),
            $filters->get(UserInputFilter::class)
        );
    }
}
