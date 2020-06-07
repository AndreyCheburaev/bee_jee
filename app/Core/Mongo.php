<?php

namespace App\Core;

use MongoDB\BSON\Unserializable;
use MongoDB\Client;
use MongoDB\Driver\Cursor;

/**
 * Класс для работы с MongoBD.
 */
class Mongo implements Unserializable
{
    /**
     * Название БД.
     */
    const DB_NAME = 'beejee';

    /**
     * @var Client
     */
    private $client;

    /**
     * Mongo constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Получаем коллекцию.
     *
     * @param $name
     *
     * @return \MongoDB\Collection
     */
    public function getCollection($name)
    {
        return $this->client->selectCollection(self::DB_NAME,$name);
    }

    /**
     * Constructs the object from a BSON array or document
     * Called during unserialization of the object from BSON.
     * The properties of the BSON array or document will be passed to the method as an array.
     *
     * @link http://php.net/manual/en/mongodb-bson-unserializable.bsonunserialize.php
     *
     * @param array $data Properties within the BSON array or document.
     */
    public function bsonUnserialize(array $data) {}

    /**
     * @param Cursor $cursor
     * @param bool   $unique
     *
     * @return array|mixed
     */
    public function handlingCursor($cursor, $unique = false)
    {
        if (true === $unique) {
            return current($cursor->toArray());
        }

        $collections = [];

        foreach ($cursor as $document) {
            $collections[] = $document;
        }

        return $collections;
    }
}