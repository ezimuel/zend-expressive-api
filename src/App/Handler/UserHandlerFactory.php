<?php
namespace App\Handler;

use App\Model\UserModel;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Helper\UrlHelper;

class UserHandlerFactory
{
    public function __invoke(ContainerInterface $container) : UserHandler
    {
        return new UserHandler(
            $container->get(UserModel::class),
            $container->get(ResourceGenerator::class),
            $container->get(HalResponseFactory::class),
            $container->get(UrlHelper::class)
        );
    }
}
