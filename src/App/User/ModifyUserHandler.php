<?php
declare(strict_types=1);

namespace App\User;

use App\RestDispatchTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Helper\UrlHelper;

class ModifyUserHandler implements RequestHandlerInterface
{
    private $model;
    private $inputFilter;

    use RestDispatchTrait;

    public function __construct(
        UserModel $model,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory,
        UserInputFilter $inputFilter
    ) {
        $this->model = $model;
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
        $this->inputFilter = $inputFilter;
    }

    public function patch(ServerRequestInterface $request) : ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $user = $this->model->updateUser($id, $request->getParsedBody(), $this->inputFilter);
        return $this->createResponse($request, $user);
    }

    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $this->model->deleteUser($id);
        return new EmptyResponse(204);
    }
}
