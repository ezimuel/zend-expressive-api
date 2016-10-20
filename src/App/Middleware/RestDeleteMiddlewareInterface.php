<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RestDeleteMiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, callable $next = null);
}