<?php

namespace App\Core;

/**
 * Class Controller
 */
class Controller
{
    /**
     * Поля сортировки задач.
     */
    const SORT_VALUES = ['_id', 'status', 'user', 'user', 'email'];

    /**
     * @var Logger
     */
    public $logger;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var View
     */
    public $view;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->logger   = new Logger();
        $this->request  = new Request();
        $this->response = new Response();
        $this->view     = new View();
    }
}