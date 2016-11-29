<?php
/**
 * Created by PhpStorm.
 * User: jomedia_97
 * Date: 28.11.16
 * Time: 11:42
 */

namespace AlexKhram\Model;


class Post extends Model
{
    protected $table = 'post';
    protected $idField = 'id';

    protected function validator($post)
    {
        $errors = '';
        if (count($post) > 3 or array_diff(array_keys($post), ['title', 'body', 'image'])) {
            $errors .= " Request must have only fields 'title', 'body' and 'image'.";
        }
        if (empty($post['title']) or empty($post['body'])) {
            $errors .= " Fields 'title' or 'body' can`t be empty";
        }
        if (!empty($post['title']) and 1 != preg_match("/^[\w ?!-]{3,200}$/u", $post['title'])) {
            $errors .= " Fields 'title' can contain only letters, digits, spaces and symbols - _ ? ! (200 symbols)";
        }
        if (!empty($errors)) {
            return ["errorValidator" => $errors];
        }
        return [];
    }
    public function getById($id)
    {
        $id = (int)$id;
        if(!$response = parent::getById($id)){
            return ["error" => "Not found post with given id.", "statusCode" => 404];
        }
        return $response;
    }
    public function insertPost($instance)
    {
        $response = parent::insert($instance);
        if(!empty($response['errorValidator'])){
            return ["error" => $response['errorValidator'], "statusCode" => 400];
        }
        if(!empty($response['error'])){
            return ["error" => "Duplicate key.", "statusCode" => 400];
        }
        return ['status'=>'Post added', 'id'=>$response['id']];
    }
    
    public function updateById($id, $instance)
    {
        if(!parent::getById($id)){
            return ["error" => "Not found post with given id.", "statusCode" => 404];
        }

        $response = parent::updateById($id,$instance);
        if(!empty($response['errorValidator'])){
            return ["error" => $response['errorValidator'], "statusCode" => 400];
        }
        if(!empty($response['error'])){
            return ["error" => "Nothing to update.", "statusCode" => 400];
        }
        return ['status'=>'Post updated', 'id'=>$response['id']];
    }
    
    public function deleteById($id)
    {
        if(!parent::getById($id)){
            return ["error" => "Not found post with given id.", "statusCode" => 404];
        }
        $response = parent::deleteById($id);
        if(!empty($response['error'])){
            return ["error" => "Not deleted. Try again later", "statusCode" => 500];
        }
        return ['status'=>'Post deleted', 'id'=>$response['id']];
    }
}