<?php

namespace Multi\Admin\Controllers;

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectID;
use Multi\Admin\Models\Orders;
use Multi\Admin\Models\Products;

/**
 * Order Contoller class
 */
class OrderController extends Controller
{
    /**
     * orderList function
     * list all orders
     * @return void
     */
    public function orderListAction()
    {
        if ($this->request->has('status')) {
            $this->statusUpdate($this->request->getPost('id'));
            $this->response->redirect('order/orderList');
        } elseif ($this->request->has('dropdown') && $this->request->get('date') == 'date') {
            $this->view->orders =  $this->dropdownSearch($this->request->get());
        } elseif (($this->request->get('date') && count($this->request->get()) == 3) || $this->request->has('to')) {
            print_r($this->request->get());
            $this->view->orders = $this->dateSearch($this->request->get());

            // die(date('Y-m-d'));
        } elseif ($this->request->has('dropdown') && $this->request->get('date') != 'date' && count($this->request->get()) > 3) {
            $this->view->orders = $this->dropdownDateSearch($this->request->get());
        } else {
            // die('her');
            $orders = new Orders($this->mongo, 'store', 'orders');
            $this->view->orders = $orders->find();
        }
        $this->view->t = $this->cache->get('en');

        // $this->view->t = $this->cache->get($this->request->get('locale'));
    }

    public function statusUpdate($arr)
    {
        $Order = new Orders($this->mongo, 'store', 'orders');
        $order = $Order->updateOne(
            ['_id' => new ObjectID((string) $this->request->getPost('id'))],
            [
                '$set' => [
                    'Status' => $this->request->getPost('status'),
                    'Status_change_date' =>  date('Y-m-d')
                ]
            ]
        );
    }

    public function dropdownSearch($arr)
    {
        $arr =  array_slice($this->request->get(), 2);
        $match = array();
        foreach ($arr as $key => $value) {
            if ($value == 'date') {
                continue;
            }
            array_push($match, array('Status' => $value));
        }
        print_r($match);
        // die;
        $orders = new Orders($this->mongo, 'store', 'orders');
        return $orders->find(array('$or' => $match));
    }

    public function dateSearch($arr)
    {
        $orders = new Orders($this->mongo, 'store', 'orders');
        $arr =  array_slice($this->request->get(), 2);
        $match = array();
        if ($arr['date'] == 'today') {
            array_push($match, array('created_date' =>  date('Y-m-d')));
            return $orders->find(array('$or' => $match));
        }
        if ($arr['date'] == 'this_week') {
            return $orders->find(array(
                'created_date' => array(
                    '$lte' => date('Y-m-d'),
                    '$gte' => date("Y-m-d", strtotime("-1 week"))
                )
            ));
        }
        if ($arr['date'] == 'this_month') {
            $monthFromToday = date("Y-m-d", strtotime("-1 month", strtotime(date("Y/m/d"))));
            return $orders->find(array(
                'created_date' => array(
                    '$lte' => date('Y-m-d'),
                    '$gte' => $monthFromToday
                )
            ));
        }
        if ($arr['date'] == 'custom') {
            $monthFromToday = date("Y-m-d", strtotime("-1 month", strtotime(date("Y/m/d"))));
            $from = $this->request->get('from');
            $to = $this->request->get('to');
            // die($to);
            return $orders->find(array(
                'created_date' => array(
                    '$lte' => $to,
                    '$gte' => $from
                )
            ));
        }
    }

    public function dropdownDateSearch($arr)
    {
        $arr =  array_slice($this->request->get(), 2);
        $match = array();
        if ($this->request->get('date') == 'today') {
            foreach ($arr as $key => $value) {
                array_push($match, array('Status' => $value, 'created_date' => '2022-04-16'));
            }

            $orders = new Orders($this->mongo, 'store', 'orders');
            return $orders->find(array('$or' => $match));
        }
        if ($this->request->get('date') == 'this_week') {
            foreach ($arr as $key => $value) {
                array_push($match, array('Status' => $value,  'created_date' => array(
                    '$lte' => date('Y-m-d'),
                    '$gte' => date("Y-m-d", strtotime("-1 week"))
                )));
            }

            $orders = new Orders($this->mongo, 'store', 'orders');
            return $orders->find(array('$or' => $match));
        }
        if ($this->request->get('date') == 'this_month') {
            $monthFromToday = date("Y-m-d", strtotime("-1 month", strtotime(date("Y/m/d"))));
            foreach ($arr as $key => $value) {
                array_push($match, array('Status' => $value, 'created_date' => array(
                    '$lte' => date('Y-m-d'),
                    '$gte' => $monthFromToday
                )));
            }
            $orders = new Orders($this->mongo, 'store', 'orders');
            return $orders->find(array('$or' => $match));
        }

        if ($this->request->get('date') == 'custom') {
            foreach ($arr as $key => $value) {
                array_push($match, array('Status' => $value, 'created_date' => array(
                    '$lte' => $this->request->get('to'),
                    '$gte' => $this->request->get('from')
                )));
            }
            $orders = new Orders($this->mongo, 'store', 'orders');
            return $orders->find(array('$or' => $match));
        }
    }
    /**
     * addOrder function
     * add new order
     * @return void
     */
    public function addOrderAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        // $orders = new Orders($this->mongo, 'store', 'orders');
        $products = new Products($this->mongo, 'store', 'products');
        $this->view->products = $products->find();

        if ($this->request->has('id')) {
            $product = new Products($this->mongo, 'store', 'products');
            $product = $product->findOne(['_id' => new ObjectID((string) $this->request->get('id'))]);
            echo json_encode($product);
            die();
        }



        if ($this->request->has('customer_name')) {
            $newOrder = new Orders($this->mongo, 'store', 'orders');
            $myescaper = new \App\Components\Myescaper();
            $myescaper = $myescaper->santize($this->request->getPost());
            $this->view->post = $this->request->getPost();
            $this->view->order = $myescaper;
            $myescaper['created_date'] = date('Y-m-d');
            $newOrder->insertOne(
                $myescaper,
                // [
                //     'customer_name',
                //     'customer_address',
                //     'zipcode',
                //     'product',
                //     'quantity',
                //     'created_date'
                // ]
            );
            // $this->view->order = $newOrder;
            // $newOrder->save();
            // $eventsManager = $this->di->get('EventsManager');
            // $settings = Settings::find(1);
            // $this->view->event =   $eventsManager->fire('notification:beforesend', $newOrder, $settings);
            // $success = $this->view->event->save();
            // if ($success) {
            //     $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Added Successfully</h6>";
            // } else {
            //     $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            // }
        }
    }

    /**
     * delete function
     * Delete a particular order
     * @return void
     */
    public function deleteAction()
    {
        $product = Orders::find($this->request->get('id'));

        $success = $product->delete();
        if ($success) {
            $this->response->redirect('http://localhost:8080/order/orderList?locale=en');
        } else {
            echo "Something went wrong";
        }
    }

    /**
     * editOrder function
     * edit and update a particular order
     * @return void
     */
    public function editOrderAction()
    {
        $this->view->t = $this->cache->get($this->request->get('locale'));
        $order = Orders::find($this->request->get('id'));
        $this->view->products = Products::find();
        $this->view->order = $order;
        if ($this->request->isPost('Update Order')) {
            $myescaper = new App\Components\Myescaper();
            $myescaper = $myescaper->santize($this->request->getPost());
            $order[0]->customer_name = $myescaper['customer_name'];
            $order[0]->customer_address = $myescaper['customer_address'];
            $order[0]->zipcode = $myescaper['zipcode'];
            $order[0]->product = $myescaper['product'];
            $order[0]->quantity = $myescaper['quantity'];
            $success = $order[0]->save();
            if ($success) {
                $this->view->msg = "<h6 class='alert alert-success w-75 container text-center'>Updated Successfully</h6>";
            } else {
                $this->view->msg = "<h6 class='alert alert-danger w-75 container text-center'>Something went wrong</h6>";
            }
        }
    }
}
