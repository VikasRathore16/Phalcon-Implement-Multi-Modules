<?php

namespace Multi\Admin\Controllers;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Controller;

/**
 * SecureController class
 * used to built acl.cache file
 */
class SecureController extends Controller
{
    /**
     * index function
     * build acl file 
     * @return void
     */
    public function indexAction()
    {
        $aclFile = APP_PATH . '/security/acl.cache';
        if (true !== is_file($aclFile)) {
            $acl = new Memory();

            $acl->addRole('admin');
            $acl->addComponent(
                'index',
                [
                    'index',
                ]
            );
            $acl->allow('admin', '*', '*');
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        } else {
            $acl = unserialize(file_get_contents($aclFile));
        }
        if (true == $acl->isallowed('manager', 'index', 'index')) {
            echo "Access granted";
        } else {
            echo "Access denied";
        }
    }
}
