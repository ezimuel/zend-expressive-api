<?php
namespace App\Middleware;

use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Model\User as UserModel;
use Zend\Expressive\Helper\UrlHelper;

class User implements RestGetMiddlewareInterface, RestPostMiddlewareInterface, RestPatchMiddlewareInterface, RestDeleteMiddlewareInterface
{
    protected $model;
    protected $helper;

    use RestDispatchTrait;

    public function __construct(UserModel $model, UrlHelper $helper)
    {
        $this->model = $model;
        $this->helper = $helper;
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        if (null === $id) {
            $users = $this->model->getAll();
            return new JsonResponse([' users' => $users ]);
        }
        $user = $this->model->getUser($id);
        if (! $user) {
            return $response->withStatus(404);
        }
        return new JsonResponse(['user' => $user ]);
    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $user = $request->getParsedBody();
        $id   = (int) $this->model->addUser($user);
        $response = $response->withHeader( 'Location', $this->helper->generate('api.user.get', ['id' => $id]));
        return $response->withStatus(201);
    }

    public function patch(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        $user = $this->model->updateUser($id, $request->getParsedBody());
        if (! $user) {
            return $response->withStatus(404);
        }
        return new JsonResponse([ 'user' => $user ]);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $id = $request->getAttribute('id');
        $result = $this->model->deleteUser($id);
        if (! $result) {
            return $response->withStatus(404);
        }
        return new EmptyResponse();
    }
}
