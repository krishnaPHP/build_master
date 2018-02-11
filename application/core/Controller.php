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
    public function isAuthenticated()
    {
        $session = new \Application\Helpers\SessionHelper();
        return $session->get('authenticated') === true;
    }

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
        return isset($_POST) && count($_POST) > 0;
    }

    public function isGet()
    {
        return isset($_GET) && count($_GET) > 0;
    }

    public function getPost($key = null)
    {
        $post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        if (count($post) == 0) return null;
        if ($key) return isset($post[$key]) ? $this->sanitize($post[$key]) : null;
        return $this->sanitize($post);
    }

    public function getGet($key = null)
    {
        $get = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        if (count($get) == 0) return null;
        if ($key) return isset($get[$key]) ? $this->sanitize($get[$key]) : null;
        return $this->sanitize($get);
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