<?php
namespace App\Exception;

use DomainException;
use Zend\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Zend\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class NoResourceFoundException extends DomainException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public static function create(string $message) : self
    {
        $e = new self($message);
        $e->status = 404;
        $e->detail = $message;
        $e->type = 'https://example.com/api/doc/resource-no-found';
        $e->title = 'Resource not found';
        return $e;
    }
}
