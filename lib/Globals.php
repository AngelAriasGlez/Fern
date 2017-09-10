<?php
namespace fw;

class Globals implements \ArrayAccess{
	private static $Instance = null;
	private $Data = array();

	private function __construct() {
	}
	
	protected function __clone() {}
	
	static public function getInstance() {
		if(is_null(self::$Instance))
		self::$Instance = new self();
		return self::$Instance;
	}
	
	//Register
	public function __set($label, $object) {
		$this->Data[$label] = $object;
	}
	
	public function __unset($label) {
		if(isset($this->Data[$label]))
		unset($this->Data[$label]);
	}
	
	public function __get($label) {
		if(isset($this->Data[$label])) return $this->Data[$label];
		return null;
	}
	
	public function __isset($label) {
		return isset($this->Data[$label]);
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function set($name, $value){$this->Data[$name] = $value;}

	/**
	 * @param $name
	 * @return bool|mixed
	 */
	public function get($name){return $this->Data[$name];}



	/*Array Access */
	public function offsetExists($offset){return  $this->__isset($offset);}
	public function offsetGet($offset){return $this->__get($offset);}
	public function offsetSet($offset,$value){$this->Data[$offset] = $value;}
	public function offsetUnset($offset){$this->__unset($offset);}

}

?>