<?php
namespace fw;



class Template {
	private $_vars = array();
	private $_path;

	public function __construct($temp = null) {
		if (is_file($temp)){
			$this->_path = $temp;
		}elseif(is_string($temp)){
            $this->_path = preg_replace('/'.TEMPLATE_EXTENSION.'$/', '', $temp).TEMPLATE_EXTENSION;
		}
		if(!file_exists($this->_path)){
			throw new \Exception('No such template file in "' . $this->_path . '" with input '.$temp);
		}
	}
	/**
	 * renderiza
	 *
	 * @param bool $AutoSetBaseTpl
	 * @return string
	 */
	public function render(){
		foreach($this->_vars as $_var => $_val){
			$$_var = $_val;
		}

		ob_start();
		ob_implicit_flush(0);
		require($this->_path);

		$result = ob_get_clean();
		return $result;

	}
	
	public function __toString(){
		return $this->render();
	}
	

	public function setVar($label, $object) {
		$this->_vars[$label] = $object;
	}
	public function setVars(array $array){
		foreach($array as $k=>$v)
			$this->_vars[$k] = $v;
	}

	/** Magic Getters & Setters */
	public function __set($label, $object) {
		if(!isset($this->_vars[$label]))
		$this->_vars[$label] = $object;
	}
	public function __unset($label) {
		if(isset($this->_vars[$label]))
		unset($this->_vars[$label]);
	}
	public function __get($label) {
		if(isset($this->_vars[$label]))
		return $this->_vars[$label];
		return false;
	}
	public function __isset($label) {
		if(isset($this->_vars[$label]))
		return true;
		return false;
	}
}
?>