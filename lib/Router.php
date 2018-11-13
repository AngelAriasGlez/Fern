<?php
namespace fw;

use fw\Router\Handle;

class Router {

	
	/*private static $CONTROLLER;
	private static $ACTION;
	private static $ARGS;
	private static $CTR_PATH;*/
	
	const MVC_DEFAULT_ACTION = 'default';
	const MVC_DEFAULT_CONTROLLER = 'index';


	private $NotFoundHandler;

    private $dataGenerator;

	public function __construct()
    {
        $this->dataGenerator = new \fw\Router\DataGenerator();
    }


    private function generate($method, $pattern, $handle){
        $routeDatas = \fw\Router\Parser::parse($pattern);
        foreach ($routeDatas as $routeData) {
            $this->dataGenerator->addRoute($method, $routeData, $handle);
        }
    }

    public function get($pattern, $handle)
    {
        $this->generate('GET', $pattern, $handle);
    }
    public function post($pattern, $handle)
    {
        $this->generate('POST', $pattern, $handle);
    }
    public function put($pattern, $handle)
    {
        $this->generate('PUT', $pattern, $handle);
    }
    public function delete($pattern, $handle)
    {
        $this->generate('DELETE', $pattern, $handle);
    }
    public function head($pattern, $handle)
    {
        $this->generate('HEAD', $pattern, $handle);
    }
    public function patch($pattern, $handle)
    {
        $this->generate('PATCH', $pattern, $handle);
    }
    public function any($pattern, $handle)
    {
        foreach(['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'] as $method) {
            $this->generate($method, $pattern, $handle);
        }
    }


    public function multiController($path, $left_pattern = ''){
	    $handle = function() use ($path){
            $args = func_get_args();

            $controller = isset($args[0]) ? $args[0] : self::MVC_DEFAULT_CONTROLLER;
            $class = 'Controller';
            $path = rtrim($path, URI_SEP).URI_SEP.$controller.'/'.$class.'.php';
            array_shift ($args);
            return $this->executeController($path, $class, $args[0]);
        };
	    $patterns = rtrim($left_pattern, '/').'[/[{c}[/[{a}[/{d:.*}]]]]]';
	    $this->any($patterns, $handle);
    }
    public function singleController($path, $left_pattern = ''){
        $handle = function() use ($path){
            $args = func_get_args();
            $class = pathinfo($path)['filename'];
            return $this->executeController($path, $class, $args[0]);

        };
        $patterns = rtrim($left_pattern, '/').'[/[{d:.*}]]';
        $this->any($patterns, $handle);
    }

    private function executeController($path, $class, $args){
        $methodSuffix = 'Action';

        if(is_file($path)) {
            include($path);
            $controllerObj = new $class();
            if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') return $controllerObj->getResponse();

            $dataGenerator = new \fw\Router\DataGenerator();

           foreach(Annotations::getMethodsAnnotations($class) as $k=>$var) {
               $var = array_change_key_case($var);
               $path = isset($var['path']) ? $var['path'] : null;
               if($path === null) continue;
               $methods = isset($var['method']) ? explode(',', $var['method']) : ['GET'];

               $h = function () use($controllerObj, $k){
                   $args = func_get_args();
                   return call_user_func_array(array($controllerObj, $k), $args);
               };
               $r = \fw\Router\Parser::parse($path);
               foreach ($r as $routeData) {
                   foreach($methods as $m) {
                       $dataGenerator->addRoute(strtoupper(trim($m)), $routeData, $h);
                   }
               }
           };

            $route = (new \fw\Router\Dispatcher($dataGenerator->getData()))->dispatch($_SERVER['REQUEST_METHOD'], '/'.reset($args));

            $params = explode('/', reset($args));
            $method = ($params[0]) ? $params[0]: self::MVC_DEFAULT_ACTION;
            array_shift($params);

            if($route[0] == \fw\Router\Dispatcher::FOUND){
                return call_user_func_array($route[1], $route[2]);
            }else if (is_callable(array($controllerObj, $method.ucfirst(strtolower($_SERVER['REQUEST_METHOD'])).$methodSuffix))) {
                return call_user_func_array(array($controllerObj, 'execute'), [$method.ucfirst(strtolower($_SERVER['REQUEST_METHOD'])).$methodSuffix, $params]);
            }else if(is_callable(array($controllerObj, $method.$methodSuffix))){
                return call_user_func_array(array($controllerObj, 'execute'), [$method.$methodSuffix, $params]);
            }
        }
        return false;
    }

    function notFound($handler){
	    $this->NotFoundHandler = $handler;
    }


    /**
     * @param $uri
     * @return Response
     */

    public function execute($uri){
        $dis = new \fw\Router\Dispatcher($this->dataGenerator->getData());
        $route = $dis->dispatch($_SERVER['REQUEST_METHOD'], $uri);
        if($route[0] == \fw\Router\Dispatcher::FOUND){
            $result = call_user_func($route[1], $route[2]);
            if($result !== false) return $result;
        }
        if($this->NotFoundHandler) return call_user_func($this->NotFoundHandler, @$route[2]);
        return Response::code(404);


    }

}
