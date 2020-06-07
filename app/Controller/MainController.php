<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\Tasks;
use Phalcon\Exception;

/**
 * Class MainController
 *
 */
class MainController extends Controller
{
    /**
     *
     *
     *
     * @return string
     */
    public function indexAction()
    {
        $page = $this->request->get('page') ?? 1;

        setcookie('page', $page);
        $sort = $_COOKIE['sort'] ?? 0;

        $skip       = 0;
        $tasks      = new Tasks();
        $tasksCount = $tasks->getCountTasks();

        if ($tasksCount > 3) {
            $this->logger->notice('count ' . print_r($tasksCount, true));

            $skip = 3 * $page - 3;
        }



        $tasksList = $tasks->getTasks('_id', -1, 3, $skip);
        $this->logger->notice('Получили список задач ' . print_r($tasksList, true));
        $pages = ceil($tasksCount / 3);

        try {
            $page  = $this->view->generate(
                'index',
                [
                    'tasks' => $tasksList,
                    'count' => $tasksCount,
                    'pages' => $pages,
                    'sort'  => $sort,
                ]
            );
        } catch (\Exception $e) {
            $this->logger->notice('Ошибка генерации шаблона ' . print_r($e,true));

            return '505 error';
        }

        return $page;
    }

    /**
     * Страница авторизации.
     *
     * @return string
     */
    public function loginAction(): string
    {
        if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
            header('Location: /');
        }

        try {
            $page = $this->view->generate('login', []);
        } catch (\Exception $e) {
            $this->logger->notice('Ошибка генерации шаблона ' . print_r($e, true));

            return '505 error';
        }

        return $page;
    }

    /**
     * Разлогиниваем админа.
     *
     * @return void
     */
    public function logoutAction(): void
    {
        $_SESSION['admin'] = false;

        header('Location: /');
    }
}