<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Phalcon\Acl\Adapter\Memory;
use Multi\Admin\Models\Roles;
use Multi\Admin\Models\Components;
use Multi\Admin\Models\Permissions;
use MongoDB\BSON\ObjectID;

/**
 * Acl class
 * Used to give permission to users
 */
class AclController extends Controller
{
    /**
     * index function
     * call acl index view
     * @return void
     */
    public function indexAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
    }

    /**
     * addRole function
     * Add Roles like User, Amdin
     * @return void
     */
    public function addRoleAction()
    {
        $request = new Request();
        $this->view->t = $this->cache->get($this->request->get('locale'));
        if (true === $request->isPost()) {
            $newrole = new Roles($this->mongo, 'store', 'roles');
            $rollarr = array(
                'role' => $request->getPost('roles'),
            );
            $newrole = $newrole->insertOne(
                $rollarr,
            );
            $success = $newrole->getInsertedCount();
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }


    /**
     * addComponent function
     * Add components based on Controller
     * @return void
     */
    public function addComponentAction()
    {
        $request = new Request();
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->controllers = $this->displayControllers();

        if (true === $request->isPost()) {
            $this->view->post = $request->getPost();
            $component = new Components($this->mongo, 'store', 'components');
            $component = $component->insertOne(
                $request->getPost(),
            );
            $success = $component->getInsertedCount();
            // $this->view->success = $success;
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }


    private function displayControllers()
    {
        $dir    = APP_PATH . '/admin/controllers';
        $files = scandir($dir, 1);
        $controllers = array();
        foreach ($files as $key => $value) {
            $explode  = explode('Controller', $value);
            array_push($controllers, strtolower($explode[0]));
        }
        return  array_diff($controllers, array('.', '..'));
    }

    /**
     * allowAction function
     * Allow users for different pages
     * @return void
     */
    public function allowAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $roles = new Roles($this->mongo, 'store', 'roles');
        $components = new Components($this->mongo, 'store', 'components');

        $this->view->roles = $roles->find();
        $this->view->components  = $components->find();
        if ($this->request->isPost('action')) {
            $actions = $this->displayActions($this->request->getPost('controller'));
            echo json_encode($actions);
            die;
        }
    }

    private function displayActions($controllerName)
    {
        $controller = strtolower($controllerName);
        $dir    = APP_PATH . '/admin/views/' . $controller;
        $files = scandir($dir, 1);
        $actions = array();
        foreach ($files as $key => $value) {
            $explode  = explode('.phtml', $value);
            array_push($actions, $explode[0]);
        }
        return array_diff($actions, array('.', '..'));
    }

    /**
     * data function
     * List all users with their permissions and add permissions to ach.cache
     * @return void
     */
    public function aclPermissionListAction()
    {
        $permission = new Permissions($this->mongo, 'store', 'permissions');
        $request = new Request();
        $this->view->t = $this->cache->get($this->request->get('locale'));
        if ($request->isPost()) {
            $role = $request->getPost('roles');
            $component  = $request->getPost('component');
            $action  = $request->getPost('action');
            $newPermission = array(
                'role' => $role,
                'component' => $component,
                'action' => $action,
            );
            $find = $permission->find([
                'role' => $role,
                'component' => $component,
                'action' => $action,
            ]);
            if (count($find->toArray()) < 1) {
                $permissionInsert = $permission->insertOne(
                    $newPermission
                );
                $success = $permissionInsert->getInsertedCount();
                if ($success) {
                    $this->updateAcl($permission);
                }
            }
        }
        $this->view->permissions = $permission->find();
    }


    /**
     * delete function
     * Delete user permissions
     * @return void
     */
    public function deleteAction()
    {
        $permission = new Permissions($this->mongo, 'store', 'permissions');
        $permission = $permission->deleteOne(
            [
                '_id' => new ObjectID($this->request->get('id'))
            ]
        );
        $success = $permission->getDeletedCount();
        if ($success) {
            $permissions = new Permissions($this->mongo, 'store', 'permissions');
            $this->updateAcl($permissions);
            $this->response->redirect('/admin/acl/aclPermissionList/?locale=' . $this->request->get('locale'));
        }
    }

    private function updateAcl($permission)
    {
        $aclFile = APP_PATH . '/admin/security/acl.cache';
        if (true === is_file($aclFile)) {
            $acl = new Memory();
            $permissions = $permission->find();
            foreach ($permissions as $permission) {
                $acl->addRole($permission->role);
                if ($permission->action == "*" && $permission->role == 'admin') {
                    $acl->allow('admin', '*', "*");
                    continue;
                }
                if ($permission->action == "*" && $permission->role == 'Admin') {
                    $acl->allow('Admin', '*', "*");
                    continue;
                }
                $acl->addComponent(
                    $permission->component,
                    $permission->action
                );
                $acl->allow($permission->role, $permission->component, $permission->action);
            }

            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(file_get_contents($aclFile));
        }
    }
}
