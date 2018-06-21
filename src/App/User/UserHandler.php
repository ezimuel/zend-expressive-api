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

class UserHandler implements RequestHandlerInterface
{
    private $model;

    use RestDispatchTrait;

    public function __construct(
        UserModel $model,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory
    ) {
        $this->model = $model;
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
    }

    public function get(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id', false);
        return false === $id
            ? $this->getAllUsers($request)
            : $this->getUser((int) $id, $request);
    }

    public function getUser(int $id, ServerRequestInterface $request): ResponseInterface
    {
        return $this->createResponse(
            $request,
            $this->model->getUser($id)
        );
    }

    public function getAllUsers(ServerRequestInterface $request): ResponseInterface
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $users = $this->model->getAll();
        $users->setItemCountPerPage(25);
        $users->setCurrentPageNumber($page);
        return $this->createResponse($request, $users);
    }
}
