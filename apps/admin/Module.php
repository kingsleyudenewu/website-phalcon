<?php

namespace Phalcon\Admin;

use Phalcon\DiInterface;
use Phalcon\Events\Manager;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface;


class Module implements ModuleDefinitionInterface
{

    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {

        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Phalcon\Admin\Controllers' => __DIR__ . '/controllers/',
            'Phalcon\Models'            => APP_PATH . '/models/',
        ));

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        $di->set('dispatcher', function ()
        {

            $eventsManager = new Manager();
            $dispatcher = new Dispatcher;
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace('Phalcon\Admin\Controllers');

            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di['view'] = function ()
        {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            return $view;
        };
    }
}
