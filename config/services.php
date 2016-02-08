<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Di\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\View;

/**
 * The FactoryDefault Dependency Injector automatically registers the right services to provide a full stack framework
 */
$di = new FactoryDefault();

/**
 * The URL component is used to generate all kinds of URLs in the application
 */
$di->setShared('url', function () use ($config)
{
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config)
{

    $view = new View();

    $view->setViewsDir($config->application->viewsDir);

    //Set template vars
    $view->view->setVar('latest', \Models\Article::find(array('published' => true, 'limit' => 5, 'order' => 'id')));

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config)
{
    $dbConfig = $config->database->toArray();

    return new \Phalcon\Db\Adapter\Pdo\Sqlite($dbConfig);
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function ()
{
    return new MetaDataAdapter();
});

/**
 * Starts the session the first time some component requests the session service
 */
$di->setShared('session', function ()
{
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function ()
{
    return new \Phalcon\Flash\Session(array(
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ));
});

/**
 * Set the default namespace for dispatcher
 */
$di->setShared('dispatcher', function () use ($di)
{
    $dispatcher = new Phalcon\Mvc\Dispatcher();
    $dispatcher->setDefaultNamespace('Phalcon\Frontend\Controllers');
    $dispatcher->setModelBinding(true);

    return $dispatcher;
});
