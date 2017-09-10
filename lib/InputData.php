<?php
/**
 * Created by PhpStorm.
 * User: Angel
 * Date: 27/04/2017
 * Time: 16:38
 */

namespace fw;


class InputData
{
	private $_data = [];
	/**
	 * @var DataValidator[]
	 */
	private $_filters = [];
	public function __construct($get_post_array){
		$this->_data = $get_post_array;
	}

	public function setFilter($name, DataValidator $filter){
		$this->_filters[$name] = $filter;
		return $this;
	}

	/**
	 * @param $name
	 * @return DataValidator
	 */
	public function get($name){
		if(!isset($this->_data[$name])) return $this->_filters[$name]->process(null);

		if(isset($this->_filters[$name])){
			return $this->_filters[$name]->process($this->_data[$name]);
		}else{
			return $this->_data[$name];
		}
	}
	public function isValid($key){
		if(isset($this->_data[$key])){
			return $this->_filters[$key]->isValid($this->_data[$key]);
		}
		return false;
	}

	public function __get($key){
		return $this->get($key);
	}
	public function __set($key, $value){
		$this->_data[$key] = $value;
	}
}