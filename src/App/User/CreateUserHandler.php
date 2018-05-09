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

class CreateUserHandler implements RequestHandlerInterface
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

    public function post(ServerRequestInterface $request) : ResponseInterface
    {
        $id = $this->model->addUser($request->getParsedBody(), $this->inputFilter);

        $response = $this->createResponse($request, $this->model->getUser($id));

        return $response->withStatus(201)->withHeader(
            'Location',
            $this->helper->generate('api.users', ['id' => $id])
        );
    }
}
