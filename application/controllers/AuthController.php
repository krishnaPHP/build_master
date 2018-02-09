<?php
/**
 * Auth Controller, used for authentication
 *
 * @Author: Krishna S
 * Date: 08-02-2018
 */

namespace Application\Controller;

class AuthController extends \Application\Core\Controller
{
    public function index()
    {
        if ($this->isAuthenticated())
            $this->redirect('dashboard');

        $view = $this->loadView('user/login');
        $view->render();
    }

    public function login()
    {
        //Todo
        if ($this->isPost()) {
            $username = $this->getPost('username');
            $password = $this->getPost('password');

            $model = $this->loadModel('\Application\Model\User');
            $users = $model->findAll();
            foreach ($users as $user) {
                if ($user->username == $username && $user->password == $password) {
                    $session = new \Application\Helper\SessionHelper();
                    $session->set('authenticated', true);
                    $session->set('username', $username);
                    break;
                }
            }

            if ($this->isAuthenticated()) {
                $this->redirect('exam');
            }
        }

        $view = $this->loadView('user/login');
        $view->render();
    }

    public function logout()
    {
        $session = new \Application\Helper\SessionHelper();
        $session->destroy();
        $this->redirect('auth');
    }

}