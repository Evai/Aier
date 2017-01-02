<?php

/**
 * Class Twig
 */
class Twig
{
    public $view;

    public $data;

    public $twig;

    public $path = BASE_PATH . '/app/Views/';

    /**
     * Twig constructor.
     * @param $view
     * @param $data
     */
    public function __construct($view, $data)
    {
        $loader = new Twig_Loader_Filesystem($this->path);

        $this->twig = new Twig_Environment($loader, array(
            'cache' => BASE_PATH . '/cache/views/',
            'debug' => true
        ));

        $this->view = $view;
        $this->data = $data;

    }

    /**
     * @param $view
     * @param array $data
     * @return Twig
     */
    public static function render($view, $data = array())
    {

        return new Twig($view, $data);

    }

    public function __destruct()
    {
        $this->twig->display($this->view, $this->data);
    }
}