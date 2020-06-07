<?php

namespace App\Model;

use App\Core\Mongo;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Driver\Cursor;
use MongoDB\Driver\Exception\InvalidArgumentException;

/**
 * Class Tasks
 *
 * Структура документа
 *  "user" : "Andrey",
 *  "name" : "Task 1",
 *  "text" : "tesx fdzs dfd ad aasda",
 *  "email" : "andr@bla.com",
 *  "status" : false,
 *  "edit" : false
 */
class Tasks extends Mongo
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * Tasks constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->collection = $this->getCollection('tasks');
    }

    /**
     * Добавляем новую задачу.
     *
     * @param $task
     *
     * @return mixed
     */
    public function addTask($task)
    {
        $insert = $this->collection->insertOne($task);

        return $insert->getInsertedId();
    }

    /**
     * Обновляем задачу.
     *
     * @param        $id
     * @param string $userName
     * @param string $taskName
     * @param string $taskText
     * @param string $email
     * @param bool   $status
     * @param bool   $edit
     *
     * @return \MongoDB\UpdateResult
     */
    public function updateTask(
        $id,
        $userName = '',
        $taskName = '',
        $taskText = '',
        $email = '',
        $status = false,
        $edit = false
    ) {
        $result = $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            [
                '$set' => [
                    'user'   => $userName,
                    'name'   => $taskName,
                    'text'   => $taskText,
                    'email'  => $email,
                    'status' => $status,
                    'edit'   => $edit,
                ],
            ]
        );

        return $result;
    }


    /**
     * Получаем выборку задач.
     *
     * @param string $sort
     * @param int    $abs
     * @param int    $limit
     * @param int    $skip
     *
     * @return array|mixed
     */
    public function getTasks($sort = '_id', $abs = -1, $limit = 3, $skip = 0)
    {
        /** @var Cursor $cursor */
        $cursor = $this->collection->find([],['sort' => [$sort => $abs],'limit' => $limit, 'skip' => $skip]);
        $cursor->setTypeMap(['root'=>'array','document'=>'array']);

        return $this->handlingCursor($cursor);
    }

    /**
     * Ищем задачу по ID.
     *
     * @param $id
     *
     * @return array
     */
    public function getTaskById($id): array
    {
        if (!$id) {
            return [];
        }

        try {
            $result = $this->collection->findOne(['_id' => new ObjectId($id)]);
        } catch (InvalidArgumentException $e) {
            return [];
        }

        return (array)$result;
    }

    /**
     * Возвращает общее кол-во задач.
     *
     * @return int
     */
    public function getCountTasks(): int
    {
        return $this->collection->countDocuments();
    }
}