<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class View
 */
class View
{
    /**
     * Путь к шаблонам.
     *
     * @var string
     */
    private $pathToTemplate = APP_PATH . '/View';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * View constructor.
     */
    public function __construct()
    {
        $loader     = new FilesystemLoader($this->pathToTemplate);
        $this->twig = new Environment($loader, []);
    }

    /**
     * Собирает данные из шаблона.
     *
     * @param string $name
     * @param array  $data
     * @param bool   $templates
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generate($name, $data, $templates = true): string
    {
        $header        = '';
        $footer        = '';
        $login         = $_SESSION['admin'] ?? false;
        $data['login'] = $login;

        if ($templates) {
            $header  = $this->twig->render('/common/header.html.twig',['login' => $login]);
            $footer  = $this->twig->render('/common/footer.html.twig');
        }

        $content = $this->twig->render($name . '.html.twig', $data);

        return $header . $content . $footer;
    }
}