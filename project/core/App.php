<?php

namespace app\core;

/**
 * Class App
 * @package app\core
 */
class App
{
    /**
     * @var self
     */
    private static  $_instance;

    /**
     * @var string Default route
     */
    public $defaultRoute = 'site/index';

    /**
     * @var Request
     */
    private $_request;

    private function __construct() {}

    /**
     * @param $route
     * @return string
     * @throws \ReflectionException
     */
    public function runAction($route)
    {
        if ($route === '') {
            $route = $this->defaultRoute;
        }

        // double slashes or leading/ending slashes may cause substr problem
        $route = trim($route, '/');
        if (strpos($route, '//') !== false) {
            return '';
        }

        if (strpos($route, '/') !== false) {
            list($id, $route) = explode('/', $route, 2);
        } else {
            $id = $route;
            $route = '';
        }

        $controller = $this->createControllerById($id);

        if (empty($controller)) {
            return $this->runAction($this->defaultRoute);
        }

        if (preg_match('/^[a-z0-9\\-_]+$/', $route) && strpos($route, '--') === false && trim($route, '-') === $route) {
            $methodName = 'action' . str_replace(' ', '', ucwords(str_replace('-', ' ', $route)));

            if (method_exists($controller, $methodName)) {
                $method = new \ReflectionMethod($controller, $methodName);
                if ($method->isPublic() && $method->getName() === $methodName) {
                    return $controller->$methodName();
                }
            }
        }

        return $this->runAction($this->defaultRoute);
    }

    /**
     * @param $id
     * @return null
     */
    protected function createControllerById($id)
    {
        $controllerClass = 'app\controllers\\' . ucfirst($id) . 'Controller';
        if (class_exists($controllerClass)) {
            return new $controllerClass($id);
        }
        return null;
    }

    /**
     * @return App
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $request
     */
    public function setRequest($request)
    {
        $this->_request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
}