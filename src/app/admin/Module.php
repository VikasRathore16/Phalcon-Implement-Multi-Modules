<?php

namespace Multi\Admin;

use Phalcon\Loader;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(
        DiInterface $container = null
    ) {
        $loader = new Loader();
        $loader->registerNamespaces(
            [
                'Multi\Admin\Controllers' => APP_PATH . '/admin/controllers/',
                'Multi\Admin\Models'      => APP_PATH . '/admin/models/',
            ]
        );

        $loader->register();
    }

    public function registerServices(DiInterface $container)
    {
        // Registering a dispatcher
        $container->set(
            'dispatcher',
            function () {
                $dispatcher = new Dispatcher();
                $dispatcher->setDefaultNamespace(
                    'Multi\Admin\Controllers'
                );
                // $dispatcher->setDefaultNamespace(
                //     'Multi\Admin\Models'
                // );

                return $dispatcher;
            }
        );

        // Registering the view component
        $container->set(
            'view',
            function () {
                $view = new View();
                $view->setViewsDir(
                    APP_PATH . '/admin/views/'
                );

                return $view;
            }
        );
    }
}
