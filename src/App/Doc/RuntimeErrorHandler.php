<?php

declare(strict_types=1);

namespace App\Doc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class RuntimeErrorHandler implements RequestHandlerInterface
{
    private const MESSAGE = <<< 'EOT'
Runtime Error

A system error has prevented the request from completing. Try again,
and contact the API administrator if you continue to observe problems.

EOT;

    /**
     * {@inheritDoc}
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return new TextResponse(self::MESSAGE);
    }
}
