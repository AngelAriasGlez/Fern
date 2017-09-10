<?php

namespace fw\Http;
class CodeException extends \Exception{

    private $Template;
	public function __construct($code = 200, Template $template = null){

		$hc = new HttpCode($code);
        $msg = $hc->getMessage();

        if(isset($tempalte)){
            $this->Template = $template;
            $this->Template->setVar('Message', $msg);
        }

		parent::__construct("$msg - $code", $code);
	}

	public function getTemplate(){
	    return $this->Template;
    }

    public function __toString()
    {
       return '<h1>'.parent::getMessage().'</h1>';
    }

}
?>