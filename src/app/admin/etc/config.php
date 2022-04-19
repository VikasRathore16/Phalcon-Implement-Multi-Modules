<?php
/**
 * config file
 */
return [
    'app' => [
        'name'     => 'Phalcon Config',
        'version'      => '1.0.0',
        'url'      => getenv('APP_URL'),
        'time'     => time(),
    ],
    'db' => [
        'host'     => 'mysql-server',
        'username' => 'root',
        'password' => 'secret',
        'dbname'   => 'Store',
    ]

];
