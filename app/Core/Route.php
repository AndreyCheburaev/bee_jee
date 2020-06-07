<?php

namespace App\Core;

/**
 * Роутиг.
 */
class Route
{
    /**
     * Имя контроллера.
     *
     * @var string
     */
    private $controllerName = '';

    /**
     * Имя экшена.
     *
     * @var string
     */
    private $actionName = '';

    /**
     * Namespace конроллеров.
     *
     * @var string
     */
    private $defaultNamespace = 'App\\Controller\\';

    /**
     * Путь к контроллерам.
     *
     * @var string
     */
    private $path = '/app/Controller/';

    /**
     * Route constructor.
     */
    public function __construct()
    {
        $requestUri = explode('?', $_SERVER["REQUEST_URI"]);
        $requestUri = explode('/', $requestUri[0]);

        $controller           = isset($requestUri[1]) && $requestUri[1] ? $requestUri[1] : 'main';
        $this->controllerName = \ucfirst($controller . 'Controller');

        $action           = isset($requestUri[2]) && $requestUri[2] ? $requestUri[2] : 'index';
        $this->actionName = $action . 'Action';


        if(!$this->existsController()) {
            var_dump(404);

            die();
        }

        if (!$this->existsAction()) {
            var_dump(404);

            die();
        }

        session_start();
    }

    /**
     * Проверяет существование контроллера.
     *
     * @return bool
     */
    private function existsController(): bool
    {
        $file = ROOT_PATH . $this->path . \ucfirst($this->controllerName . '.php');

        return \file_exists($file);
    }

    /**
     * Проверяте у класса наличие метода.
     *
     * @return bool
     */
    private function existsAction(): bool
    {
        $name = $this->defaultNamespace . $this->controllerName;

        return \method_exists($name,$this->actionName);
    }

    /**
     * Запускает нужный контроллер.
     *
     * @return mixed
     */
    public function handle()
    {
        $name       = $this->defaultNamespace . $this->controllerName;
        $controller = new $name;

        return $controller->{$this->actionName}();
    }
}