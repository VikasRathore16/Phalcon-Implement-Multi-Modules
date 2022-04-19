<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Session\Manager;
use Phalcon\Cache;
use Phalcon\Cache\Adapter\Memory;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Mvc\Router;

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

require APP_PATH . '/library/vendor/autoload.php';


// Config  ----------------------------------------------start --------------------------------------

$config = new Config([]);
$fileName = APP_PATH . '/admin/etc/config.php';
$factory  = new ConfigFactory();
$config = $factory->newInstance('php', $fileName);

// Config  ----------------------------------------------end -----------------------------------------



//Logger ------------------------------------------------start ---------------------------------------

$adapter = new Stream(APP_PATH .'/admin/storage/main.log');
$logger  = new Logger(
    'messages',
    [
        'main' => $adapter,
    ]
);

//Logger ------------------------------------------------ends ----------------------------------------


// Loader-----------------------------------------------start ---------------------------------------- 

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/admin/Contollers",
        APP_PATH . "/admin/models/",
        APP_PATH . "/frontend/Contollers",
        APP_PATH . "/frontend/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/admin/components',
        'App\Listeners'  => APP_PATH . '/admin/listeners',
    ]
);

$loader->register();

// Loader---------------------------------------------ends --------------------------------------------


#container--------------------------------------------------start -------------------------------------

$container = new FactoryDefault();



$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$container->set(
    'mongo',
    function () {
        // $mongo = new MongoDB\Client("mongodb://localhost:27017");
        $mongo = new \MongoDB\Driver\Manager("mongodb+srv://cluster0.gbzl3.mongodb.net/myFirstDatabase", array("username" => 'root', "password" => "Vikas@1998"));

        return $mongo;
    },
    true
);

$container->set(
    'config',
    $config,
    true
);

$container->set(
    'logger',
    $logger,
);


//Event Mangement -----------------------------------------start ------------------------------------------------------

$eventsManager = new EventsManager();

$container->set(
    'db',
    function () use ($eventsManager) {
        $config = $this->get('config');
        $connection = new Mysql(
            [
                'host'     => $config->db->host,
                'username' => $config->db->username,
                'password' => $config->db->password,
                'dbname'   => $config->db->dbname,
            ]
        );
        $connection->setEventsManager($eventsManager);
        return $connection;
    }
);

$eventsManager->attach(
    'notification',
    new \App\Listeners\NotificationListeners()
);


$eventsManager->attach(
    'db:afterQuery',
    function (Event $event, $connection) use ($logger) {
        // die('db');
        $logger->error($connection->getSQLStatement());
    }
);


$eventsManager->attach(
    'application:beforeHandleRequest',
    new \App\Listeners\NotificationListeners()
);

$container->set(
    'locale',
    (new \App\Components\Locale())->getTranslator()
);
$container->set(
    'EventsManager',
    $eventsManager,
);

//Event Mangement -----------------------------------------ends ------------------------------------------------------


//Cache ---------------------------------------------------starts ----------------------------------------------------



$serializerFactory = new SerializerFactory();
$options = [
    'lifetime' => 7200,
    'storageDir'        => APP_PATH . '/admin/storage/cache',
];

$adapter = new Memory($serializerFactory, $options);
$adapter = new Phalcon\Cache\Adapter\Stream($serializerFactory, $options);
$cache = new Cache($adapter);
$container->set('cache', $cache);


//Cache ---------------------------------------------------ends ----------------------------------------------------


//session ----------------------------------------------start ----------------------------------------


$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new \Phalcon\Session\Adapter\Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->setName('user')
            ->start();

        return $session;
    }
);


//session --------------------------------------------- end ----------------------------------------------------

//Router --------------------------------------------- start ----------------------------------------------------

$container->set(
    'router',
    function () {
        $router = new Router();

        $router->setDefaultModule('frontend');

        $router->add(
            '/login',
            [
                'module'     => 'admin',
                'controller' => 'login',
                'action'     => 'index',
            ]
        );

        $router->add('/admin', array(
            'module' => "admin",
            'controller' => 'login',
            'action' => "index",
        ));

        $router->add('/admin/:controller', array(
            'module' => "admin",
            'controller' => 1,
            'action' => "index"
        ));
        $router->add('/admin/:controller/:params', array(
            'module' => "admin",
            'controller' => 1,
            'action' => 2,
            'params' => 3
        ));

        $router->add('/admin/:controller/:action/', array(
            'module' => "admin",
            'controller' => 1,
            'action' => 2
        ));

        $router->add('/admin/:controller/:action/:params', array(
            'module' => "admin",
            'controller' => 1,
            'action' => 2,
            'params' => 3
        ));

        return $router;
    }
);



$application = new Application($container);


$application->registerModules(
    [
        'frontend' => [
            'className' => \Multi\Frontend\Module::class,
            'path'      => APP_PATH . '/frontend/Module.php',
        ],
        'admin'  => [
            'className' => \Multi\Admin\Module::class,
            'path'      => APP_PATH . '/admin/Module.php',
        ]
    ]
);

//Router ------------------------------ ---------------ends ------------------------------




$application->setEventsManager($eventsManager);

//container ------------------------------------------------ends ------------------------------------------------------





try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e;
}
