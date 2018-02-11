<?php
/**
 * This is core application used to run the application
 */

namespace Framework;

class Bootstrap
{
    protected static function init()
    {
        // Define path constants
        define("DS", DIRECTORY_SEPARATOR);

        define("ROOT", getcwd() . DS);

        define("APP_PATH", ROOT . 'application' . DS);

        define("PUBLIC_PATH", ROOT . "public" . DS);

        define("FRAMEWORK_PATH", ROOT . "framework" . DS);


        define("CONFIG_PATH", APP_PATH . "config" . DS);

        define("VIEW_PATH", APP_PATH . "views" . DS);

        define("UPLOAD_PATH", PUBLIC_PATH . "uploads" . DS);

        // Define controller, action and params for example:
        // index.php?controller=users&action=list
        define("CONTROLLER", isset($_REQUEST['controller']) && $_REQUEST['controller'] !== ""
            ? ucfirst($_REQUEST['controller']) : 'Dashboard');

        define("ACTION", isset($_REQUEST['action']) && $_REQUEST['action'] !== ""
            ? $_REQUEST['action'] : 'index');

        define("PARAMS", isset($_REQUEST['params']) && $_REQUEST['params'] !== ""
            ? $_REQUEST['params'] : null);

        // Start session
        session_start();
    }

    // Routing and dispatching
    protected static function dispatch()
    {
        // Instantiate the controller class and call its action method
        $controller_name = "\\Application\\Controllers\\" . CONTROLLER . "Controller";

        $controller = new $controller_name;
        $action = ACTION;
        $params = PARAMS ? array_filter(explode(",", PARAMS)) : array();

        call_user_func_array(array($controller, $action), $params);
    }
}