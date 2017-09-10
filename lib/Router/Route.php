<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 14/05/2018
 * Time: 16:48
 */

namespace fw\Router;


class Route
{
    /** @var string */
    public $httpMethod;
    /** @var string */
    public $regex;
    /** @var array */
    public $variables;
    /** @var mixed */
    public $handler;
    /**
     * Constructs a route (value object).
     *
     * @param string $httpMethod
     * @param mixed  $handler
     * @param string $regex
     * @param array  $variables
     */
    public function __construct($httpMethod, $handler, $regex, $variables)
    {
        $this->httpMethod = $httpMethod;
        $this->handler = $handler;
        $this->regex = $regex;
        $this->variables = $variables;
    }
    /**
     * Tests whether this route matches the given string.
     *
     * @param string $str
     *
     * @return bool
     */
    public function matches($str)
    {
        $regex = '~^' . $this->regex . '$~';
        return (bool) preg_match($regex, $str);
    }
}