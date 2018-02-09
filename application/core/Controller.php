<?php
/**
 * Base Controller for all our controllers
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Core;

class Controller extends \Framework\Core\Controller
{
    public function loadModel($model)
    {
        return new $model();
    }

    public function loadView($view)
    {
        return new View($view);
    }

    public function getConfig($key = null)
    {
        return \Application\Core\Config::getConfig($key);
    }

    public function isPost()
    {
        return isset($_POST);
    }

    public function isGet()
    {
        return isset($_GET) && count($_GET) > 0;
    }

    public function getPost($key = null)
    {
        if (!isset($_POST) || count($_POST) == 0) {
            return null;
        }
        return isset($_POST[$key]) ? $this->sanitize($_POST[$key]) : $this->sanitize($_POST);
    }

    public function getGet($key = null)
    {
        if (!isset($_GET) || count($_GET) == 0) {
            return null;
        }
        return isset($_GET[$key]) ? $this->sanitize($_GET[$key]) : $this->sanitize($_GET);
    }

    public function getFile($key = null)
    {
        if (!isset($_FILES) || $_FILES[$key]['error']) {
            return null;
        }
        return isset($_FILES[$key]) ? $this->sanitize($_FILES[$key]) : $this->sanitize($_FILES);
    }

    public function redirect($controller, $method = "index", $args = array())
    {
        $baseUrl = Config::getConfig('base_url');
        $location = $baseUrl . "/" . $controller . "/" . $method . "/" . implode("/", $args);
        header("Location: " . $location);
        exit;
    }

    public function isAuthenticated()
    {
        $session = new \Application\Helper\SessionHelper();
        return $session->get('authenticated') === true;
    }

    private function sanitize($value)
    {
        // sanitize array or string values
        if (is_array($value)) {
            array_walk_recursive($value, 'sanitize_value');
        } else {
            $this->sanitize_value($value);
        }

        return $value;
    }

    private function sanitize_value(&$value)
    {
        $value = trim(htmlspecialchars($value));
    }
}