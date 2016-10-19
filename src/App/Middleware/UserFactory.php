<?php
namespace App\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;
use App\Model\User as UserModel;
use Zend\Expressive\Helper\UrlHelper;

class UserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $adapter = ($container->has(AdapterInterface::class))
            ? $container->get(AdapterInterface::class)
            : null;
        $urlHelper = ($container->has(UrlHelper::class))
            ? $container->get(UrlHelper::class)
            : null;

        return new User(new UserModel($adapter), $urlHelper);
    }
}
