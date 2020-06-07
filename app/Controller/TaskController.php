<?php

namespace App\Controller;

use App\Core\Controller;
use App\Model\Tasks;

/**
 * Class TaskController
 */
class TaskController extends Controller
{
    /**
     *
     *
     * @return string
     */
    public function addAction(): string
    {
        $this->logger->notice('Пришёл запрос ' . print_r($this->request->get(),true));

        $userName = $this->request->getPost('user_name');
        $taskName = $this->request->getPost('task_name');
        $taskText = $this->request->getPost('task_text');
        $email    = $this->request->getPost('email');
        $sort     = $_COOKIE['sort'] ?? 0;

        $tasks  = new Tasks();
        $taskId = $tasks->addTask(
            [
                'user'   => $userName,
                'name'   => $taskName,
                'text'   => $taskText,
                'email'  => $email,
                'status' => false,
                'edit'   => false,
            ]
        );
        $this->logger->notice('Пришёл запрос ' . print_r($taskId, true));


        $tasksList = $tasks->getTasks();
        $tasksCount = $tasks->getCountTasks();

        $pages = ceil($tasksCount / 3);
        $list = $this->view->generate(
            'tasks_list',
            ['tasks' => $tasksList, 'count' => $tasksCount, 'pages' => $pages, 'sort' => $sort],
            false
        );

        return $this->response->setJsonContent(
            [
                'success' => true,
                'data'    => ['list' => $list, 'task_id' => $taskId],
                'error'   => null,
            ]
        );
    }

    public function editAction()
    {
        $this->logger->notice('Пришёл запрос ' . print_r($this->request->get(),true));

        if (!isset($_SESSION['admin']) || $_SESSION['admin'] === false) {
            return $this->response->setJsonContent(
                [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Редактировать может только администратор.',
                ]
            );
        }

        $userName = $this->request->getPost('user_name');
        $taskName = $this->request->getPost('task_name');
        $taskText = $this->request->getPost('task_text');
        $email    = $this->request->getPost('email');
        $taskId   = $this->request->getPost('task_id');
        $status   = $this->request->getPost('status') == 1;

        $tasks = new Tasks();
        $task = $tasks->getTaskById($taskId);

        $edit = $task['edit'];
        if (!$task['edit']) {
            $edit = $taskText !== $task['text'];
        }

        $this->logger->notice('$edit ' . print_r($edit,true));

        $tasks->updateTask($taskId,$userName,$taskName,$taskText,$email,$status,$edit);


        return $this->response->setJsonContent(
            [
                'success' => true,
                'data'    => null,
                'error'   => null,
            ]
        );
    }

    /**
     *
     * @return string
     */
    public function getAction(): string
    {
        $this->logger->notice('Пришёл запрос ' . print_r($this->request->get(),true));

        $taskId = $this->request->get('id');

        if (!$taskId) {
            $this->logger->notice('Не был получен id задачи.');

            return '404';
        }

        $tasks = new Tasks();
        $task = $tasks->getTaskById($taskId);
        $this->logger->notice('get task ' . print_r($task,true));

        if (!$task) {
            return 'not found';
        }

        $task['id'] = $taskId;

        try {
            $page = $this->view->generate('task', ['task' => $task]);
        } catch (\Exception $e) {
            $this->logger->notice('Ошибка генерации шаблона ' . print_r($e,true));

            return '505 error';
        }

        return $page;
    }

    public function sortAction()
    {
        $this->logger->notice('Пришёл запрос ' . print_r($this->request->get(),true));
        $page = $this->request->getPost('page') ?? 1;
        $sort = $this->request->getPost('sort') ?? 0;

        $skip = 0;
        $tasks = new Tasks();
        $tasksCount = $tasks->getCountTasks();

        if ($tasksCount > 3) {
            $this->logger->notice('count ' . print_r($tasksCount,true));

            $skip = 3 * $page - 3;
        }
        $abs = $sort == 3 ? 1: -1;
        $tasksList = $tasks->getTasks(self::SORT_VALUES[$sort],$abs,3,$skip);

        $this->logger->notice('Получили список задач ' . print_r($tasksList,true));


        //$this->logger->notice('Кол-во док-в ' . print_r($tasksCount,true));

        $pages = ceil($tasksCount / 3);
        $list = $this->view->generate(
            'tasks_list',
            ['tasks' => $tasksList, 'count' => $tasksCount, 'pages' => $pages, 'sort' => $sort],
            false
        );

        return $this->response->setJsonContent(
            [
                'success' => true,
                'data'    => ['list' => $list],
                'error'   => null,
            ]
        );
    }
}