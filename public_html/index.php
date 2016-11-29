<?php
ini_set('display_errors',1);

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../app/config.php';

$app = new Silex\Application();

$app['debug'] = false;


// handling json request
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// services providers
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => $dbParams
));

// models
$app['modelPost'] = new \AlexKhram\Model\Post($app);

// post api
$postApiV1 = $app['controllers_factory'];
$postApiV1->get('/', "AlexKhram\\Controller\\PostController::getPosts");
$postApiV1->get('/{postId}', "AlexKhram\\Controller\\PostController::getPostById")->assert('postId', '\d+');
$postApiV1->post('/', "AlexKhram\\Controller\\PostController::postPost");
$postApiV1->put('/{postId}', "AlexKhram\\Controller\\PostController::updatePost");
$postApiV1->delete('/{postId}', "AlexKhram\\Controller\\PostController::deletePosts");
$app->mount('/post/api/v1', $postApiV1);

// errors handling
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    switch ($code) {
        case 404:
            return $app->json(['error'=>'Page not found'], 404);
        default:
            return $app->json(['error'=>'Something went wrong'], 500);
    }
});

$app->run();

