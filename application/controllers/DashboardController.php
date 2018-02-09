<?php
/**
 * DashboardController for dashboard or login
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Controllers;

class DashboardController extends \Application\Core\Controller
{
    public function index()
    {
        $view = $this->loadView('dashboard/index');
        $view->render();
    }

    public function greet($message)
    {
        echo "Greeting from application: " . $message;
    }

}