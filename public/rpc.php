<?php

use Zend\Expressive\AppFactory;
use Zend\Diactoros\Response\JsonResponse;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

$app = AppFactory::create();
$app->get('/api/ping', function ($request, $response, $next) {
  return new JsonResponse(['ack' => time()]);
});
$app->get('/api/hello[/[{name}]]', function ($request, $response, $next) {
  $name = $request->getAttribute('name') ?? 'Mr. Robot';
  return new JsonResponse(['hello' => $name ]);
});

$app->pipeRoutingMiddleware();
$app->pipeDispatchMiddleware();
$app->run();
