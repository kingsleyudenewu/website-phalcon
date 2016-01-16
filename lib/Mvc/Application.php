<?php

namespace Lib\Mvc;

use Phalcon\Config\Adapter\Ini;
use Phalcon\Di;
use Phalcon\Mvc\Router;

/**
 * Class Application
 * @package Lib\Mvc
 */
class Application extends \Phalcon\Mvc\Application
{

    const MODULE_GUEST = 'frontend';
    const MODULE_ADMIN = 'admin';

    private static $modules = [
        'frontend' => [
            'className' => 'Phalcon\Frontend\Module',
            'path'      => __DIR__ . '/../../apps/frontend/Module.php'
        ],
        'admin'    => [
            'className' => 'Phalcon\Admin\Module',
            'path'      => __DIR__ . '/../../apps/admin/Module.php'
        ]
    ];

    /**
     * Register the services here to make them general or register in
     * the ModuleDefinition to make them module-specific.
     */
    protected function registerServices()
    {
        $di = $this->getDI();
        $di->set('router', function ()
        {
            /* @var $this Di */
            $router = new \Phalcon\Mvc\Router();
            $defaultModule = self::MODULE_GUEST;

            $session = $this->get('session');

            if ($auth = $session->get('auth'))
            {
                /*
                 * If we had more user types we would do a switch here
                 * and check user type to set associated module
                 */
                $defaultModule = self::MODULE_ADMIN;
            }
            $router->setDefaultModule($defaultModule);

            return $router;
        }, true);

        $this->setDI($di);
    }

    public function main()
    {
        $this->registerServices();
        $this->registerModules(self::$modules);

        return $this->handle()->getContent();
    }
}
