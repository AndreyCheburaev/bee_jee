<?php

namespace App\Core;

/**
 * Класс для работы с запросами.
 */
class Request
{
    /**
     * Возвращает значение поля в запросе или возвращает все данные запроса.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function get($name = null)
    {
         if ($name) {
             return $_REQUEST[$name] ?? null;
         }

         return $_REQUEST;
    }

    /**
     * Возвращает значение поля post запроса или возвращает все данные post запроса.
     *
     * @param null|string $name
     *
     * @return mixed
     */
    public function getPost($name = null)
    {
        if ($name) {
            return $_POST[$name] ?? null;
        }

        return $_POST;
    }
}