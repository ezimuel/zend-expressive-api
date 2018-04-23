<?php
namespace App\Exception;

use DomainException;
use Zend\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Zend\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class MissingParameterException extends DomainException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public static function create(string $message) : self
    {
        $e = new self($message);
        $e->status = 400;
        $e->detail = $message;
        $e->type = 'https://example.com/api/doc/missing-parameter';
        $e->title = 'Missing parameter';
        return $e;
    }
}
