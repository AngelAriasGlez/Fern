<?php
namespace fw;
class Json{
	protected $Data = array();
	
	public function __construct($value = array()){
		if(is_string($value)){
			$this->Data = json_decode($value, true);
		}else if(is_array($value)){
			$this->Data = $value;
		}else if($value instanceof \stdClass){
			$this->Data = json_decode(json_encode($value), true);
		}
	}
	public function fromArray(array $array){
		$this->Data = $array;
		return $this;
	}
	public function toArray(){
	    return $this->Data;
    }
	public function __toString(){
		return $this->toJsonString();
	}
    public function toJsonString(){
        return json_encode($this->Data);
    }
}
?>