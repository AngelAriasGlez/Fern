<?php
namespace fw;
use fw\Http\Header;
use fw\Http\Headers;

class Request {

    public function __construct()
    {

    }
    public function getHeaders(){
        $headers = new Headers();
        foreach (getallheaders() as $name => $value) {
            $headres->add(new Header($name, $value));
        }
        return $headers;
    }

    public function getBody(){
        $body = file_get_contents('php://input');
        if(@$_SERVER["CONTENT_TYPE"] == 'application/json') {
            return json_decode($body, true);
        }
        return $body;
    }

    function getMethod(){
        return $_SERVER["REQUEST_METHOD"];
    }


}
