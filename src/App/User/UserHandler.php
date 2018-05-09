<?php
namespace App\User;

use App\Exception;
use App\RestDispatchTrait;
use DomainException;
use PharIo\Manifest\Email;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\ResourceGenerator\Exception\OutOfBoundsException;
use Zend\Expressive\Helper\UrlHelper;
use Zend\InputFilter\Input;
use Zend\Validator;

class UserHandler implements RequestHandlerInterface
{
    private $model;
    private $resourceGenerator;
    private $responseFactory;
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
        $user = $this->model->getUser((int) $id);
        if (empty($user)) {
            throw Exception\NoResourceFoundException::create('User not found');
        }

        return $this->createResponse($request, $user);
    }

    public function getAllUsers(ServerRequestInterface $request): ResponseInterface
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $users = $this->model->getAll();
        $users->setItemCountPerPage(25);
        $users->setCurrentPageNumber($page);
        try {
            return $this->createResponse($request, $users);
        } catch (OutOfBoundsException $e) {
            throw Exception\OutOfBoundsException::create($e->getMessage());
        }
    }

    public function post(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $request->getAttribute('id', false);
        if (false !== $id) {
            throw Exception\MethodNotAllowedException::create('You cannot POST on a specific user, use PATCH instead');
        }
        $user = $request->getParsedBody();
        // Filter input data
        $this->inputFilter->setData($request->getParsedBody());
        if (! $this->inputFilter->isValid()) {
            throw Exception\InvalidParameterException::create(
                'Invalid parameter',
                $this->inputFilter->getMessages()
            );
        }

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
        $response = $this->createResponse($request, $this->model->getUser($id));
        return $response->withStatus(201)->withHeader(
            'Location',
            $this->helper->generate('api.users', ['id' => $id])
        );
    }

    public function patch(ServerRequestInterface $request) : ResponseInterface
    {
        $id = (int) $request->getAttribute('id');

        $this->inputFilter->setData($request->getParsedBody());
        $this->inputFilter->get('email')->setRequired(false);
        $this->inputFilter->get('password')->setRequired(false);
        if (! $this->inputFilter->isValid()) {
            throw Exception\InvalidParameterException::create(
                'Invalid parameter',
                $this->inputFilter->getMessages()
            );
        }

        try {
            $user = $this->model->updateUser($id, $request->getParsedBody());
        } catch (DomainException $e) {
            throw Exception\MissingParameterException::create(
                'Missing parameter'
            );
        }
        if (empty($user)) {
            throw Exception\NoResourceFoundException::create('User not found');
        }
        return $this->createResponse($request, $user);
    }

    public function delete(ServerRequestInterface $request) : ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $result = $this->model->deleteUser($id);
        if (! $result) {
            throw Exception\NoResourceFoundException::create('User not found');
        }
        return new EmptyResponse(204);
    }

    protected function createResponse(ServerRequestInterface $request, object $user): ResponseInterface
    {
        return $this->responseFactory->createResponse(
            $request,
            $this->resourceGenerator->fromObject($user, $request)
        );
    }
}
