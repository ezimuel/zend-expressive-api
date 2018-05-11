<?php

declare(strict_types=1);

namespace App\Doc;

use Psr\Container\ContainerInterface;

class ResourceNotFoundHandlerFactory
{
    public function __invoke(ContainerInterface $container) : ResourceNotFoundHandler
    {
        return new ResourceNotFoundHandler();
    }
}
