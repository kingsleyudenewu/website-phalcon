<?php


namespace Lib;


use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model;

/**
 * Class Dispatcher
 * @package Lib
 *
 * This class exists to add additional functionality at lines 248-268 to allow
 * automatic model injection into controller functions.
 */
class Dispatcher extends \Phalcon\Mvc\Dispatcher
{

    public function dispatch()
    {
        $wasFresh = false;

        $dependencyInjector = $this->_dependencyInjector;
        if (! $dependencyInjector)
        {
            $this->_throwDispatchException("A dependency injection container is required to access related dispatching services", self::EXCEPTION_NO_DI);

            return false;
        }

        // Calling beforeDispatchLoop
        $eventsManager = $this->_eventsManager;
        if ($eventsManager)
        {
            if ($eventsManager->fire("dispatch:beforeDispatchLoop", this) === false)
            {
                return false;
            }
        }

        $numberDispatches = 0;
        $actionSuffix = $this->_actionSuffix;

        $this->_finished = false;

        while (! $this->_finished)
        {

            $numberDispatches++;

            // Throw an exception after 256 consecutive forwards
            if ($numberDispatches == 256)
            {
                $this->_throwDispatchException("Dispatcher has detected a cyclic routing causing stability problems", self::EXCEPTION_CYCLIC_ROUTING);
                break;
            }

            $this->_finished = true;

            $this->_resolveEmptyProperties();

            $namespaceName = $this->_namespaceName;
            $handlerName = $this->_handlerName;
            $actionName = $this->_actionName;
            $handlerClass = $this->getHandlerClass();

            // Calling beforeDispatch
            if ($eventsManager)
            {

                if ($eventsManager->fire("dispatch:beforeDispatch", $this) === false)
                {
                    continue;
                }

                // Check if the user made a forward in the listener
                if ($this->_finished === false)
                {
                    continue;
                }
            }

            // Handlers are retrieved as shared instances from the Service Container
            $hasService = (bool) $dependencyInjector->has($handlerClass);
            if (! $hasService)
            {
                // DI doesn't have a service with that name, try to load it using an autoloader
                $hasService = (bool) class_exists($handlerClass);
            }

            // If the service can be loaded we throw an exception
            if (! $hasService)
            {
                $status = $this->_throwDispatchException($handlerClass . " handler class cannot be loaded", self::EXCEPTION_HANDLER_NOT_FOUND);
                if ($status === false)
                {

                    // Check if the user made a forward in the listener
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }
                break;
            }

            // Handlers must be only objects
            $handler = $dependencyInjector->getShared($handlerClass);

            // If the object was recently created in the DI we initialize it
            if ($dependencyInjector->wasFreshInstance() === true)
            {
                $wasFresh = true;
            }

            if (! $handler)
            {
                $status = $this->_throwDispatchException("Invalid handler returned from the services container", self::EXCEPTION_INVALID_HANDLER);
                if ($status === false)
                {
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }
                break;
            }

            $this->_activeHandler = $handler;

            // Check if the params is an array
            $params = $this->_params;
            if (! is_array($params))
            {

                // An invalid parameter variable was passed throw an exception
                $status = $this->_throwDispatchException("Action parameters must be an Array", self::EXCEPTION_INVALID_PARAMS);
                if ($status === false)
                {
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }
                break;
            }

            // Check if the method exists in the handler
            $actionMethod = $actionName . $actionSuffix;

            if (! method_exists($handler, $actionMethod))
            {

                // Call beforeNotFoundAction
                if ($eventsManager)
                {

                    if ($eventsManager->fire("dispatch:beforeNotFoundAction", $this) === false)
                    {
                        continue;
                    }

                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }

                // Try to throw an exception when an action isn't defined on the object
                $status = $this->_throwDispatchException("Action '" . $actionName . "' was not found on handler '" . $handlerName . "'", self::EXCEPTION_ACTION_NOT_FOUND);
                if ($status === false)
                {
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }

                break;
            }

            // Calling beforeExecuteRoute
            if ($eventsManager)
            {

                if ($eventsManager->fire("dispatch:beforeExecuteRoute", $this) === false)
                {
                    continue;
                }

                // Check if the user made a forward in the listener
                if ($this->_finished === false)
                {
                    continue;
                }
            }

            // Calling beforeExecuteRoute as callback and event
            if (method_exists($handler, "beforeExecuteRoute"))
            {

                if ($handler->beforeExecuteRoute($this) === false)
                {
                    continue;
                }

                // Check if the user made a forward in the listener
                if ($this->_finished === false)
                {
                    continue;
                }
            }

            /**
             * Call the 'initialize' method just once per request
             */
            if ($wasFresh === true)
            {

                if (method_exists($handler, "initialize"))
                {
                    $handler->initialize();
                }

                /**
                 * Calling afterInitialize
                 */
                if ($eventsManager)
                {
                    if ($eventsManager->fire("dispatch:afterInitialize", $this) === false)
                    {
                        continue;
                    }

                    // Check if the user made a forward in the listener
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                }
            }

            //Check if we can bind a model based on what the controller action is expecting
            $reflectionMethod = new \ReflectionMethod($handlerClass, $actionMethod);
            $methodParams = $reflectionMethod->getParameters();
            foreach ($methodParams as $key => $methodParam)
            {
                if ($methodParam->getClass() && $class = $methodParam->getClass()->getName())
                {
                    //If we are in a base class and the child implements BindModelInterface we getModelName
                    if ($class == Model::class)
                    {
                        if (in_array(\Phalcon\Mvc\Controller\BindModelInterface::class, class_implements($handlerClass)))
                        {
                            $model = call_user_func([$handlerClass, 'getModelName']);
                            $bindModel = call_user_func_array([$model, "findFirst"], [$params[$key]]);
                            $params[$key] = $bindModel;
                            break;
                        }
                    }

                    //Model type is defined, fetch it
                    if (is_subclass_of($class, Model::class))
                    {
                        $bindModel = $class::findFirst([$params[$key]]);
                        $params[$key] = $bindModel;
                        break;
                    }
                }
            }


            try
            {
                foreach ($methodParams as $key => $methodParam)
                {
                    if ($methodParam->getClass() && $class = $methodParam->getClass()->getName())
                    {
                        if (is_subclass_of($class, Model::class))
                        {
                            $bindModel = $class::findFirst([$params[$key]]);
                            $params[$key] = $bindModel;
                            break;
                        } elseif (in_array(\Phalcon\Mvc\Controller\BindModelInterface::class, class_implements($handlerClass)))
                        {
                            $class = call_user_func_array([$handlerClass, 'getModelName'], []);

                            if (is_subclass_of($class, Model::class))
                            {
                                $bindModel = $class::findFirst([$params[$key]]);
                                $params[$key] = $bindModel;
                                break;
                            }
                        }

                    }
                }


                // We update the latest value produced by the latest handler
                $this->_returnedValue = call_user_func_array([$handler, $actionMethod], $params);
                $this->_lastHandler = $handler;

            } catch (\Exception $e)
            {
                if ($this->_handleException($e) === false)
                {
                    if ($this->_finished === false)
                    {
                        continue;
                    }
                } else
                {
                    throw $e;
                }
            }

            // Calling afterExecuteRoute
            if ($eventsManager)
            {

                if ($eventsManager->fire("dispatch:afterExecuteRoute", $this, $value) === false)
                {
                    continue;
                }

                if ($this->_finished === false)
                {
                    continue;
                }

                // Call afterDispatch
                $eventsManager->fire("dispatch:afterDispatch", $this);
            }

            // Calling afterExecuteRoute as callback and event
            if (method_exists($handler, "afterExecuteRoute"))
            {

                if ($handler->afterExecuteRoute($this, $value) === false)
                {
                    continue;
                }

                if ($this->_finished === false)
                {
                    continue;
                }
            }
        }

        // Call afterDispatchLoop
        if ($eventsManager)
        {
            $eventsManager->fire("dispatch:afterDispatchLoop", $this);
        }

        return $handler;
    }
}