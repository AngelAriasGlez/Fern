<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 12/02/2018
 * Time: 15:07
 */

namespace fw\Router;


class Mvc extends Handle
{
    private $Directory;
    private $Single;

    public static $DEFAULT_CONTROLLER = 'index';
    public static $DEFAULT_ACTION = 'default';
    public function __construct($directory, $single = null)
    {
        $this->Directory = $directory;
        $this->Single = $single;

    }



    public function execute(){
        $args = func_get_args();
        if($this->Single !== null) array_unshift($args, $this->Single);

        $controller = isset($args[0]) ? $args[0] : self::$DEFAULT_ACTION;
        $class = 'Controller';
        $dir = rtrim($this->Directory, URI_SEP).URI_SEP.$controller.'/'.$class.'.php';
        $method = isset($args[1]) ? $args[1] : self::$DEFAULT_ACTION;
        $methodSuffix = 'Action';

            if(is_file($dir)) {
                include($dir);
                $controllerObj = new $class();
                var_dump(ucfirst(strtolower($_SERVER['REQUEST_METHOD'])));

                if (is_callable(array($controllerObj, $method.ucfirst(strtolower($_SERVER['REQUEST_METHOD'])).$methodSuffix))
                || is_callable(array($controllerObj, $method.$methodSuffix))
                ) {
                    $args = array_slice($args, 2, count($args) - 2);
                    return call_user_func_array(array($controllerObj, $method), $args); //Call Action
                }
            }
            return false;
    }
}