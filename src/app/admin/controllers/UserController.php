<?php

namespace Multi\Admin\Controllers;

use Firebase\JWT\JWT;
use Phalcon\Mvc\Controller;
use Multi\Admin\Models\Users;
use Multi\Admin\Models\Roles;

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
        $users = new Users($this->mongo, 'store', 'users');
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->users = $users->find();
    }

    /**
     * addUser function
     * Add a new user and generate token
     * @return void
     */
    public function addUserAction()
    {
        $roles = new Roles($this->mongo, 'store', 'roles');
        $this->view->roles = $roles->find();
        $this->view->t = $this->cache->get($this->request->get('locale'));
        if ($this->request->isPost()) {
            $user = new Users($this->mongo, 'store', 'users');
            $newUser = array(
                'username' => $this->request->getPost('username'),
                'role' => $this->request->getPost('roles'),
                'jwt' => $this->createToken($this->request->getPost()),
            );
            $user =  $user->insertOne(
                $newUser
            );
            $success =  $user->getInsertedCount();
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }

    private function createToken($postArray)
    {
        $payload = array(
            "username" => $postArray['username'],
            "email" => $postArray['email'],
            "password" => $postArray['password'],
            "role" => $postArray['roles'],
        );
        //ecoding array
        $key = "example_key";
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }
}
