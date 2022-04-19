<?php

namespace Multi\Frontend\Controllers;

use Phalcon\Mvc\Controller;


/**
 * Index Controllerclass
 * funtions list
 * indexAction
 * dashboardAction
 * settingsAction
 */
class IndexController extends Controller
{
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
}
