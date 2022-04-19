<?php
namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;

use Multi\Admin\Models\Orders;
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
            $myescaper = new App\Components\Myescaper();
            $arr = $myescaper->santize($request->getPost());
            $settings = Settings::find(1);
            $settings[0]->title_optimization = $arr['title'];
            $settings[0]->default_price = $arr['default_price'];
            $settings[0]->default_stock = $arr['default_stock'];
            $settings[0]->default_zip = $arr['default_zipcode'];
            $success = $settings[0]->save();
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Upated Successfully</h6>";
            }
            if (!$success) {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }
}
