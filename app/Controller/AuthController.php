<?php

namespace App\Controller;

use App\Core\Controller;

/**
 * Class AuthController
 */
class AuthController extends Controller
{
    /** @var string */
    const LOGIN    = 'admin';

    /** @var string */
    const PASSWORD = '123';

    /**
     * Авторизация администратора.
     *
     * @return string
     */
    public function loginAction()
    {
        $this->logger->notice('Пришёл запрос ' . print_r($this->request->get(),true));

        $login    = $this->request->getPost('login') ?? null;
        $password = $this->request->getPost('password') ?? null;

        if (!$login || !$password || $login !== self::LOGIN || $password !== self::PASSWORD) {
            return $this->response->setJsonContent(
                [
                    'success' => false,
                    'data'    => null,
                    'error'   => null,
                ]
            );
        }

        $_SESSION['admin'] = true;

        return $this->response->setJsonContent(
            [
                'success' => true,
                'data'    => null,
                'error'   => null,
            ]
        );
    }
}