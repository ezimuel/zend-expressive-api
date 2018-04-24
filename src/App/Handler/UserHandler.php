<?php
namespace App\Handler;

use DomainException;
use App\Exception;
use App\Model\UserModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\ResourceGenerator\Exception\OutOfBoundsException;
use Zend\Expressive\Helper\UrlHelper;

class UserHandler implements RequestHandlerInterface
{
    private $model;
    private $resourceGenerator;
    private $responseFactory;
    private $helper;

    use RestDispatchTrait;

    public function __construct(
        UserModel $model,
        ResourceGenerator $resourceGenerator,
        HalResponseFactory $responseFactory,
        UrlHelper $helper
    ) {
        $this->model = $model;
        $this->resourceGenerator = $resourceGenerator;
        $this->responseFactory = $responseFactory;
        $this->helper = $helper;
    }

    public function get(ServerRequestInterface $request) : ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        if (! $id) {
            $page = $request->getQueryParams()['page'] ?? 1;
            $users = $this->model->getAll();
            $users->setItemCountPerPage(25);
            $users->setCurrentPageNumber($page);
            try {
                return $this->responseFactory->createResponse(
                    $request,
                    $this->resourceGenerator->fromObject($users, $request)
                );
            } catch (OutOfBoundsException $e) {
                throw Exception\OutOfBoundsException::create($e->getMessage());
            }
        }
        $user = $this->model->getUser($id);
        if (empty($user)) {
            throw Exception\NoResourceFoundException::create('User not found');
        }

        return $this->responseFactory->createResponse(
            $request,
            $this->resourceGenerator->fromObject($user, $request)
        );
    }

    public function post(ServerRequestInterface $request) : ResponseInterface
    {
        $user = $request->getParsedBody();
        try {
            $id = $this->model->addUser($user);
        } catch (DomainException $e) {
            throw Exception\MissingParameterException::create($e->getMessage());
        }
        if ($id === null) {
            throw Exception\RuntimeException::create(
                'Ops, something went wrong. Please contact the administrator'
            );
        }
        $response = new EmptyResponse(201);
        return $response->withHeader(
            'Location',
            $this->helper->generate('api.user', ['id' => $id])
        );
    }

    public function patch(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id');
        try {
            $user = $this->model->updateUser($id, $request->getParsedBody());
        } catch (DomainException $e) {
            throw Exception\MissingParameterException::create($e->getMessage());
        }
        if (empty($user)) {
            throw Exception\NoResourceFoundException::create('User not found');
        }
        return new JsonResponse(['user' => $user]);
    }

    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id');
        $result = $this->model->deleteUser($id);
        if (! $result) {
            throw Exception\NoResourceFoundException::create('User not found');
        }
        return new EmptyResponse(204);
    }
}
