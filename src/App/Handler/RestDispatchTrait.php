<?php
namespace App\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\EmptyResponse;

trait RestDispatchTrait
{
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $method = strtolower($request->getMethod());
        if (method_exists($this, $method)) {
            return $this->$method($request);
        }
        return new EmptyResponse(501); // Method not implemented
    }
}
