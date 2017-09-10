<?php
namespace fw;
class Redirect{
	private $Url;
	
	public function __construct($url){
        $this->Url = $url;
	}

    public function getUrl(){
	    return $this->Url;
    }
}
?>