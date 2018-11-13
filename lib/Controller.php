<?php
namespace fw;
use fw\Http\Header;
use fw\Http\HeaderCode;

abstract class Controller {
	private $Vars = array();
	private $BaseTemplate = null;
	private $Response;
    private $Request;
	
	public function __construct()
	{

	}
	public function getResponse(){
	    if(!$this->Response){
            $this->Response = new Response();
        };
	    return $this->Response;
    }
    public function getRequest(){
        if(!$this->Request){
            $this->Request = new Request();
        };
        return $this->Request;
    }
    public function getRequestMethod(){
        return $this->getRequest()->getMethod();
    }
    public function isRequestMethod($method){
        return strtolower($this->getRequest()->getMethod()) == strtolower($method);
    }

    public function allowCors(){
        $hs = $this->getResponse()->getHeaders();
        $hs->add(Header::create('Access-Control-Allow-Origin', @($_SERVER["HTTP_ORIGIN"])?$_SERVER["HTTP_ORIGIN"]:'*'));
        $hs->add(Header::create('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE, PUT'));
        $hs->add(Header::create('Access-Control-Allow-Headers', 'Content-Type, authorization'));
        $hs->add(Header::create('Access-Control-Allow-Credentials', 'true'));
    }
    public function setResponseCode($code){
        $this->getResponse()->getHeaders()->add(HeaderCode::create($code));
    }
    public function addHeader(Header $header){
        $this->getResponse()->getHeaders()->add($header);
    }

	public function View($template = NULL, $vars = null){
	    if($template === null){
            $backtrace = debug_backtrace();
            //debug_print_backtrace();
            $actionName = str_replace('Action', '', $backtrace[1]['function']);
            $template = dirname($backtrace[0]['file']).DIR_SEP.$actionName;
        }else if(!is_file($template)){
            $backtrace = debug_backtrace();
            //debug_print_backtrace();
            $template = dirname($backtrace[0]['file']).DIR_SEP.$template;
        }
        $template = new Template($template);

        $template->setVar('self', $this);
		foreach($this->Vars as $name=>$value){
			$template->setVar($name, $value);
		}
		if(is_array($vars)){
			$template->setVars($vars);
		}

		if($this->BaseTemplate != null && $this->BaseTemplate instanceof BaseTemplate){
            $this->BaseTemplate->setContent($template->render());
		    return $this->BaseTemplate;
        }
        return $template;
	}
	public function setVar($label, $val){
		$this->Vars[$label] = $val;
	}
	public function setVars(array $array){
		foreach($array as $k=>$v)
			$this->Vars[$k] = $v;
	}
	public function setBaseTemplate($file){
	    $this->BaseTemplate = new BaseTemplate($file);
    }

    public function unsetBaseTemplate(){
        $this->BaseTemplate = null;
    }
    public function getBaseTemplate(){
	    return $this->BaseTemplate;
    }

    public function execute($action, $params){
        $body = call_user_func_array(array($this, $action), $params); //Call Action
        $response = $this->getResponse();
        if($body !== null) $response->setBody($body);
        return $response;
    }
	//function default_action(){ throw new Error(404);}
}
