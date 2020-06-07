<?php

namespace App\Core;

/**
 * Class Response
 */
class Response
{
    /**
     * Готовит данные для отправки в формате JSON.
     *
     * @param $content
     *
     * @return string
     */
    public function setJsonContent($content): string
    {
        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($content);
    }
}