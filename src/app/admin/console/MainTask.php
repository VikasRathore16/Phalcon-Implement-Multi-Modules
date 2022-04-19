<?php

namespace App\Console;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// use App\Models;
use Phalcon\Cli\Task;
use Settings;
use Products;
use Orders;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action asddas' . PHP_EOL;
    }
    public function regenerateAction(int $count = 0)
    {
        echo 'This is the retenerate action ' . $count . PHP_EOL;
    }

    public function createTokenAction($user = "user")
    {

        $payload = array(
            "role" => $user,
        );

        //ecoding array 
        $key = "example_key";
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt . PHP_EOL;
    }

    public function settingsUpdateAction($default_price = 100, $default_stock = 100, $default_zipcode = 226010)
    {
        $settings = Settings::find(1);
        $settings[0]->default_price = $default_price;
        $settings[0]->default_stock = $default_stock;
        $settings[0]->default_zipcode = $default_zipcode;
        $success = $settings[0]->save();
        if ($success) {
            echo "Updated successfully" . PHP_EOL;
        } else {
            echo "Something went wrong" . PHP_EOL;
        }
    }


    public function countProductsAction()
    {
        $products = Products::find();
        $total = 0;
        foreach ($products as $product) {
            if ($product->stocks < 10) {
                echo $product->product_name . PHP_EOL;
                $total += 1;
            }
        }
        echo "Number of Products whose stock value is less than 10 is " . $total . PHP_EOL;
    }

    public function removeCacheAction()
    {
        $success = unlink(APP_PATH . "/security/acl.cache");
        if ($success) {
            echo "Deleted successfully" . PHP_EOL;
        }
    }

    public function removeLogsAction()
    {
        foreach (glob(APP_PATH . "/storage/*") as $file) {
            $total = 0;
            if (is_file($file)) {
                $success = unlink($file);
                $total += 1;
            }
        }
        if ($success) {
            echo "Deleted successfully " . $total . " files" . PHP_EOL;
        }
    }

    public function todayOrderAction()
    {
        $orders = Orders::find(
            [
                "conditions" => "created_date = :date:",
                "bind" => [
                    "date" => Date('Y-m-d')
                ]
            ]
        );

        // php cli.php main latestOrder
        print_r($orders);
        $total = 0;
        foreach ($orders as $order) {
            $total +=1;
            echo "order id = ".$order->order_id . PHP_EOL;
        }
        echo "total Orders today = ".$total . PHP_EOL;
    }
}
