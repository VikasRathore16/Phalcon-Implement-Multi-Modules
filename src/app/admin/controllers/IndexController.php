<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Http\Request;
use Multi\Admin\Models\Settings;
use MongoDB\BSON\ObjectID;

/**
 * Index Controllerclass
 * funtions list
 * indexAction
 * dashboardAction
 * settingsAction
 */

class IndexController extends Controller
{

    /**
     * indexAction function
     * call index view and set language to English
     * @return void
     */
    public function indexAction()
    {

        if ($this->request->has('locale')) {
            if ($this->cache->has($this->request->get('locale'))) {
                $this->view->t = $this->cache->get($this->request->get('locale'));
            }

            if (!$this->cache->has($this->request->get('locale'))) {
                $this->cache->clear();
                $this->cache->set($this->request->get('locale'), $this->locale);
                $this->view->t = $this->cache->get($this->request->get('locale'));
            }
        } else {
            $this->cache->set('en', $this->locale);
            $this->view->t = $this->cache->get('en');
        }
    }


    /**
     * dashboardAction function
     * call dashboard view
     * @return void
     */
    public function dashboardAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
    }

    /**
     * settings function
     * set default settings in Settings Table
     * @return void
     */
    public function settingsAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        if ($this->request->isPost()) {
            $request = new Request();
            $settings = new Settings($this->mongo, 'store', 'settings');
            $myescaper = new \App\Components\Myescaper();
            $arr = $myescaper->santize($request->getPost());
            $updateSettings = $settings->updateOne(
                ['_id' => new ObjectID("625e3fb6a25624f5feb7aaf4")],
                [
                    '$set' => [
                        'title_optimization' => $arr['title'],
                        'default_price' =>  $arr['default_price'],
                        'default_stock' => $arr['default_stock'],
                        'default_zipcode' => $arr['default_zipcode']
                    ]
                ]
            );
            if ($updateSettings->getMatchedCount()) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Upated Successfully</h6>";
            }
            if (!$updateSettings->getMatchedCount()) {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }
}
