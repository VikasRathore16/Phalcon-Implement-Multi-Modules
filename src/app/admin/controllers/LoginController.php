<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Multi\Admin\Models\Users;


/**
 * LoginContoller class
 */
class LoginController extends Controller
{
    /**
     * index function
     * handle login form request
     * @return void
     */
    public function indexAction()
    {
        $locale = $this->request->has('locale') ? $this->request->get('locale') : 'en';
        $this->view->t = $this->cache->get($locale);
        $request = new Request();
        if ($request->isPost()) {
            $myescaper = new \App\Components\Myescaper();
            $request = $myescaper->santize($request->getPost());
            $user = $this->checkLogin($request);
            if ($user != null) {
                $this->response->redirect("http://localhost:8080/admin/index/dashboard?locale=$locale");
            }
        }
    }


    public function logoutAction()
    {
        $this->session->destroy();
        $this->response->redirect('http://localhost:8080/');
    }


    /**
     * checkLogin function
     * check user token and decode it for user role
     * @param [array] $request
     * @return void
     */
    protected function checkLogin($request)
    {
        unset($this->session->user);
        try {
            $key = "example_key";
            $users = Users::find();
            foreach ($users as $user) {
                $decoded = JWT::decode($user->jwt, new Key($key, 'HS256'));
                $decoded_array = (array) $decoded;
                $email = $decoded_array['email'];
                $password = $decoded_array['password'];
                if ($email == $request['email'] && $password == $request['password']) {
                    $role = $decoded_array['role'];
                    $username = $decoded_array['username'];
                    $this->session->set('role', $role);
                    $this->session->set('username', $username);
                    break;
                }
            }
        } catch (\Exception $e) {
            echo "Try with accessable token please !";
            die;
        }
        return $this->session->get('username');
    }
}
