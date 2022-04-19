<?php

use Phalcon\Cli\Console;
use Phalcon\Cli\Dispatcher;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Exception as PhalconException;
use Phalcon\Loader;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Config\ConfigFactory;


define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
require APP_PATH . '/library/vendor/autoload.php';

$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
        APP_PATH . "/console",
    ]
);
$loader->registerNamespaces(
    [
        'App\Console' => APP_PATH . '/console/',
        'Phalcon\Mvc\Model' => APP_PATH . '/models/',

    ]
);
$loader->register();

$container  = new CliDI();
$dispatcher = new Dispatcher();

$dispatcher->setDefaultNamespace('App\Console');
$container->setShared('dispatcher', $dispatcher);

$config = new Config([]);
$fileName = '../app/etc/config.php';
$factory  = new ConfigFactory();
$config = $factory->newInstance('php', $fileName);



$container->setShared('config', function () {
    return include APP_PATH . '/etc/config.php';
});



$container->setshared(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'events',
            ]
        );
    }
);



$console = new Console($container);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k === 1) {
        $arguments['task'] = $arg;
    } elseif ($k === 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

try {
    $console->handle($arguments);
} catch (PhalconException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
} catch (Throwable $throwable) {
    fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
    exit(1);
} catch (Exception $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
