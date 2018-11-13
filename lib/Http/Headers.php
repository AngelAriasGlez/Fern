<?php
/**
 * @revisado
 *
 */
namespace fw\Http;
class Headers {
    private $Headers = [];

	public function remove($name){
		unset($this->Headers[$name]);
	}
	public function add($header){
        $this->Headers[] = $header;
	}

	public function send(){
	    foreach ($this->Headers as $head){
	        $head->send();
        }
    }
}
?>