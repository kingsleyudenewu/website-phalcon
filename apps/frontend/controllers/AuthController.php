<?php

namespace Phalcon\Frontend\Controllers;

use Models\User;
use Phalcon\Mvc\Controller;

class AuthController extends Controller
{

    public function startAction()
    {
        if ($this->request->isPost())
        {
            // Get the data from the user
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Find the user in the database
            $user = User::findFirst([
                    "(email = :email:)",
                    'bind' => [
                        'email' => $email,
                    ]]
            );
            /* @var $user User */

            //use password_verify to make this work with the laravel passwords
            if ($user && password_verify($password, $user->getPassword()))
            {

                $this->_registerSession($user);

                $this->flash->success('Welcome ' . $user->getName());

                // Forward to the 'invoices' controller if the user is valid
                return $this->dispatcher->forward([
                    'controller' => 'index',
                    'action'     => 'index'
                ]);
            }

            $this->flash->error('Wrong email/password');
        }

        // Forward to the login form again
        return $this->dispatcher->forward([
            'controller' => 'auth',
            'action'     => 'index'
        ]);
    }

    private function _registerSession(User $user)
    {
        $this->session->set('auth', [
                'id'   => $user->getId(),
                'name' => $user->getName()
            ]
        );
    }


    public function indexAction()
    {

    }
}