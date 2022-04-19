<?php

namespace Multi\Admin\Controllers;

use Firebase\JWT\JWT;
use Phalcon\Mvc\Controller;

/**
 * UserController class
 */
class UserController extends Controller
{
    /**
     * index function
     * List all users
     * @return void
     */
    public function indexAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->users = Users::find();
    }

    /**
     * addUser function
     * Add a new user and generate token
     * @return void
     */
    public function addUserAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->roles = Roles::find();
        if ($this->request->isPost()) {
            //collecting data from POST
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $roles = $this->request->getPost('roles');

            $payload = array(
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "role" => $roles,
            );

            //ecoding array 
            $key = "example_key";
            $jwt = JWT::encode($payload, $key, 'HS256');

            $newUser = array(
                'jwt' => $jwt,
            );

            $user = new Users();
            $user->assign(
                $newUser,
                [
                    'jwt'
                ]
            );
            $success =  $user->save();
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }
}
