<?php
namespace fw;
use fw\Http\Headers;

class Response {
    private $Headers;
    private $Body;

    public function __construct($body = null)
    {
        $this->Body = $body;
        $this->Headers = new Headers();
    }
    public function getHeaders(){
        return $this->Headers;
    }
    public function getBody(){
        return $this->Body;
    }

    /**
     * @param Headers $headers
     */
    public function setHeaders(Headers $headers){
        $this->Headers = $headers;
    }

    public function setBody($body){
       $this->Body = $body;
    }

    public static function code($code = 200){
        $response = new self();
        $response->getHeaders()->add(\fw\Http\HeaderCode::create($code));
        return $response;
    }

}
