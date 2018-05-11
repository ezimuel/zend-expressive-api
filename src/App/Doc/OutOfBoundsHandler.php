<?php

declare(strict_types=1);

namespace App\Doc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class OutOfBoundsHandler implements RequestHandlerInterface
{
    private const MESSAGE = <<< 'EOT'
Parameter Out Of Range

Usually, this indicates that the "page" specified in the request is
invalid. Consider fetching the first page of the collection to
determine how many pages are available, and what the last page
in the collection is.

EOT;

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return new TextResponse(self::MESSAGE);
    }
}
