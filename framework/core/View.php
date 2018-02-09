<?php
/**
 * View
 *
 * @Author: Krishna
 * Date : 08-02-2018
 */

namespace Framework\Core;


class View
{
    private $pageVars = array();
    private $viewPath = VIEW_PATH;
    private $view;
    public $config = array();

    public function __construct($view)
    {
        $this->view = "{$this->viewPath}/{$view}.php";
    }

    public function set($var, $val)
    {
        $this->pageVars[$var] = $val;
    }

    public function render()
    {
        $this->config = Config::getConfig();

        extract($this->pageVars);

        ob_start();

        $this->renderHeader();

        include $this->view;

        $this->renderFooter();

        echo ob_get_clean();
    }

    private function renderHeader()
    {
        include_once "{$this->viewPath}/layout/header.php";
    }

    private function renderContent()
    {
        include $this->view;
    }

    private function renderFooter()
    {
        include_once "{$this->viewPath}/layout/footer.php";
    }

}