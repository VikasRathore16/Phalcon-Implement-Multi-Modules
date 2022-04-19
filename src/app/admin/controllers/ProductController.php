<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectID;
use Multi\Admin\Models\Products;


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

    /**
     * addProduct function
     * add a new product
     * @return void
     */
    public function addProductAction()
    {
        // $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->t = $this->cache->get('en');

        if ($this->request->has('product_name')) {
            $newProduct = new Products($this->mongo, 'store', 'products');
            $myescaper = new \App\Components\Myescaper();
            $myescaper = $myescaper->santize($this->request->get());
            $count = $myescaper['count'];
            $variation_count = $myescaper['variation_count'];

            $additional_field = [];
            $variation_field = [];
            for ($i = 1; $i <= $count; $i++) {
                $label = $myescaper['label' . $i];
                $value = $myescaper['value' . $i];

                unset($myescaper['label' . $i]);
                unset($myescaper['value' . $i]);
                unset($myescaper['count']);
                $additional_field[$label] = $value;
            }
            for ($i = 1; $i <= $variation_count; $i++) {
                $label = $myescaper['variation_label' . $i];
                $value = $myescaper['variation_value' . $i];
                $price = $myescaper['variation_price' . $i];
                unset($myescaper['variation_label' . $i]);
                unset($myescaper['variation_value' . $i]);
                unset($myescaper['variation_price' . $i]);
                unset($myescaper['variation_count']);
                $variation_field[$i] = [
                    $label => $value,
                    'price' => $price
                ];
            }

            // print_r($additional_field);
            // print_r($variation_field);
            // die();
            $myescaper += ['variation' => $variation_field];
            $myescaper += ['additional' => $additional_field];


            $newProduct->insertOne(
                $myescaper,
                // [
                //     'product_name',
                //     'description',
                //     'tags',
                //     'price',
                //     'stocks'
                // ]
            );

            // // $newProduct->save();
            // $eventsManager = $this->di->get('EventsManager');
            // $settings = Settings::find(1);
            // $this->view->event =   $eventsManager->fire('notification:beforesend', $newProduct, $settings);
            // $success = $this->view->event->save();
            // if ($success) {
            //     $this->response->redirect("http://localhost:8080/product/productList?locale=" . $this->request->get('locale') . "&msg=success");
            // } else {
            //     $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            // }
        }
    }


    /**
     * delete function
     * delete a particular product
     * @return void
     */
    public function deleteAction()
    {
        $product = new Products($this->mongo, 'store', 'products');
        $product = $product->deleteOne(['_id' => new ObjectID((string) $this->request->get('id'))]);

        // // $success = $product->delete();
        // if ($success) {
        //     $this->response->redirect('http://localhost:8080/product/productList?locale=en');
        // } else {
        //     echo "Something went wrong";
        // }
    }

    /**
     * editProduct function
     * edit and update a particular product
     * @return void
     */
    public function editProductAction()
    {
        // $this->cache->set($this->request->get('locale'), $this->locale);
        // $this->view->t = $this->cache->get($this->request->get('locale'));
        $this->view->t = $this->cache->get('en');

        $collection = new Products($this->mongo, 'store', 'products');
        $product = $collection->findOne(["_id" =>  new ObjectID((string) $this->request->get('id'))]);

        // $product = Products::find($this->request->get('id'));
        $this->view->product = $product;
        if ($this->request->isPost('Update Product')) {
            // die('hello');
            $myescaper = new App\Components\Myescaper();
            $myescaper = $myescaper->santize($this->request->getPost());
            $updateResult = $collection->replaceOne(
                ['_id' => new ObjectID((string) $this->request->get('id'))],
                [
                    'product_name' => $myescaper['product_name'],
                    'description' => $myescaper['description'],
                    'tags' => $myescaper['tags'],
                    'price' => $myescaper['price'],
                    'stocks' => $myescaper['stocks']
                ],
            );
            // $product->product_name = $myescaper['product_name'];
            // $product->description = $myescaper['description'];
            // $product->tags = $myescaper['tags'];
            // $product->price = $myescaper['price'];
            // $product->stocks = $myescaper['stocks'];
            // $success = $product[0]->save();
            // if ($success) {
            //     $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            // } else {
            //     $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            // }
        }
    }

    // public function quickViewAction()
    // {
    //     if ($this->request->has('id')) {
    //         $product = new Products($this->mongo, 'store', 'products');
    //         $product = $product->findOne(['_id' => new ObjectID((string) $this->request->get('id'))]);
    //         json_decode($product);
    //     }
    // }

    // public function popupAction()
    // {
    // }
}
