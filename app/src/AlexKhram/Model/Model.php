<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 19.11.2016
 * Time: 19:36
 */

namespace AlexKhram\Model;


abstract class Model
{
    protected $connection;
    /**
     * @var string name of table
     */
    protected $table;
    /**
     * @var string primary key of table
     */
    protected $idField;

    public function __construct($app)
    {
        $this->connection = $app['db'];
        if (empty($this->table)) {
            throw new \Exception('Not Specified table name');
        }
        if (empty($this->idField)) {
            throw new \Exception('Not Specified primary key field');
        }
    }

    /**
     * @param int $limit
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function getAll($limit = 10, $orderBy = '', $order = 'DESC')
    {
        $limit = (int)$limit;
        if (empty($orderBy)) {
            $orderBy = $this->idField;
        }
        return $this->connection->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$order} LIMIT {$limit}");
    }

    /**
     * @param int $id
     * @return array assoc of instance
     */
    public function getById($id)
    {
        $id = (int)$id;
        return $this->connection->fetchAssoc("SELECT * FROM {$this->table} WHERE {$this->idField} = ?", [$id]);
    }

    /**
     * @param array $instance assoc array
     * @return array id of inserted data ['id' => $insertedId] or error ["error" => "Duplicate key"]
     * @throws \Exception
     */
    public function insert($instance)
    {
        if ($errors = $this->validator($instance)) {
            return $errors;
        }
        try {
            $this->connection->insert($this->table, $instance);
        } catch (\Doctrine\DBAL\DBALException $e) {
            if ($e->getPrevious() &&  '23000' === $e->getPrevious()->getCode() ) {
                return ["error" => "Duplicate key"];
            }
            throw new \Exception($e);
        }

        $insertedId = $this->connection->lastInsertId();
        return ['id' => $insertedId];
    }

    /**
     * @param int $id
     * @param $instance
     * @return array with id of updated data ['id' => $updateId] or error ["error" => "Not updated"]
     */
    public function updateById($id, $instance)
    {
        if ($errors = $this->validator($instance)) {
            return $errors;
        }
        $id = (int) $id;
        $response = $this->connection->update($this->table, $instance, array($this->idField => $id));
        if(0 == $response){
            return ["error" => "Not updated"];
        }
        return ['id' => $id];
    }

    /**
     * @param int $id
     * @return array with id of deleted data ['id' => $id] or error ["error" => "Not deleted"]
     */
    public function deleteById($id)
    {
        $id = (int)$id;
        $response = $this->connection->delete($this->table, array($this->idField => $id));
        if(0 == $response){
            return ["error" => "Not deleted"];
        }
        return ['id' => $id];
    }

    /**
     * @param array $instance assoc array
     * @return array of errors validation ['errorValidator'=> 'All errors'] or empty array
     */
    abstract protected function validator($instance);
}