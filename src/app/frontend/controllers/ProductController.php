<?php

namespace Multi\Frontend\Controllers;

use Phalcon\Mvc\Controller;
use Multi\Frontend\Models\Products;

/**
 * Product Controller class
 */
class ProductController extends Controller
{
    // public function __construct()
    // {
    //     $Products = new Products($this->mongo, 'store', 'products');
    // }
    /**
     * productList function
     * list all products
     * @return void
     */
    public function productListAction()
    {

        $Products = new Products($this->mongo, 'store', 'products');
        $this->view->t = $this->cache->get('en');
        if ($this->request->has('query')) {
            $this->view->products = $Products->find(array('$or' => array(array('product_name' => $this->request->get('query')))));
        }
        if (!$this->request->has('query') || $this->request->get('submit') == 'all') {
            $this->view->products = $Products->find();
        }

        // $this->cache->set($this->request->get('locale'), $this->locale);
        // $this->view->t = $this->cache->get($this->request->get('locale'));
        if ($this->request->get('msg')) {
            $this->view->msg = "<h6 class='alert alert-success w-25 container text-center'>Added Successfully</h6>";
        }
    }
}
