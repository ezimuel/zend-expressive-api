<?php
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
    private $helper;
    private $inputFilter;

    use RestDispatchTrait;

    public function __construct(
        UserModel $model,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory,
        UrlHelper $helper,
        UserInputFilter $inputFilter
    ) {
        $this->model = $model;
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
        $this->helper = $helper;
        $this->inputFilter = $inputFilter;
    }

    public function get(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id', false);
        if (false === $id) {
            return $this->getAllUsers($request);
        }

        return $this->createResponse(
            $request,
            $this->model->getUser((int) $id)
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

    public function post(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $this->model->addUser($request->getParsedBody(), $this->inputFilter);

        $response = $this->createResponse($request, $this->model->getUser($id));

        return $response->withStatus(201)->withHeader(
            'Location',
            $this->helper->generate('api.users', ['id' => $id])
        );
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
