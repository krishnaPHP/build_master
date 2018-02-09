<?php
/**
 * UserController for application access
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Controllers;

class UserController extends \Application\Core\Controller
{
    public function index()
    {
        $model = $this->loadModel("Application\Models\User");
        $users = $model->getUsers();

        $view = $this->loadView('user/index');
        $view->set('users', $users);
        $view->render();
    }
}