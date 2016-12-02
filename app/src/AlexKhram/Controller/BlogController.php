<?php
/**
 * Created by PhpStorm.
 * User: jomedia_97
 * Date: 29.11.16
 * Time: 12:56
 */

namespace AlexKhram\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class BlogController
{
    public function index(Request $request, Application $app)
    {

        return $app['twig']->render('index.twig', array(
            'someData' => 'test',
        ));
    }

    public function post(Request $request, Application $app, $postId)
    {
        return $app['twig']->render('post.twig', array(
            'someData' => 'test',
        ));
    }

}