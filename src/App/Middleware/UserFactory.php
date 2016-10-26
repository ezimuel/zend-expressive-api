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
        $userModel = $container->get(UserModel::class);
        $urlHelper = $container->get(UrlHelper::class);

        return new User($userModel, $urlHelper);
    }
}
