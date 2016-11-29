<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 19:30
 */

namespace AlexKhram\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class PostController
{
    public function getPosts(Request $request, Application $app)
    {
        return $app->json($app['modelPost']->getAll());
    }

    public function getPostById(Request $request, Application $app, $postId)
    {
        $response = $app['modelPost']->getById($postId);
        if(isset($response["error"])){
            return $app->json(['error'=>$response["error"]], $response["statusCode"]);
        }

        return $app->json($response, 200);
    }
    
    public function postPost(Request $request, Application $app)
    {
        $response = $app['modelPost']->insertPost($request->request->all());
        if(isset($response["error"])){
            return $app->json(['error'=>$response["error"]], $response["statusCode"]);
        }

        return $app->json($response, 201);
    }

    public function updatePost(Request $request, Application $app, $postId)
    {
        $response = $app['modelPost']->updateById($postId, $request->request->all());
        if(isset($response["error"])){
            return $app->json(['error'=>$response["error"]], $response["statusCode"]);
        }

        return $app->json($response, 200);
    }

    public function deletePosts(Request $request, Application $app, $postId)
    {
        $response =  $app['modelPost']->deleteById($postId);
        if(isset($response["error"])){
            return $app->json(['error'=>$response["error"]], $response["statusCode"]);
        }

        return $app->json($response, 200);
    }

}