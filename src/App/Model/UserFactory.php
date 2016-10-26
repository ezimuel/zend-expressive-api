<?php
namespace App\Model;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\AdapterInterface;

class UserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $adapter = $container->get(AdapterInterface::class);
        return new User($adapter);
    }
}
