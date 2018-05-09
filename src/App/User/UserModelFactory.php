<?php
namespace App\User;

use Psr\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;

class UserModelFactory
{
    public function __invoke(ContainerInterface $container) : UserModel
    {
        return new UserModel(
            $container->get(AdapterInterface::class)
        );
    }
}
