<?php

namespace App\Core;

/**
 * Class Logger
 *
 */
class Logger
{
    /**
     * Путь к лог-файлу.
     *
     * @var string
     */
    private $file = ROOT_PATH . '/log/log.txt';

    /**
     * Записывает данные в конец лог-файла.
     *
     * @param string $data
     */
    public function notice($data)
    {
        $data = PHP_EOL . '['.date('D M d H:i:s Y',time()).'] ' . $data;

        $file = fopen($this->file, "a");
        fwrite($file,$data);
        fclose($file);
    }
}