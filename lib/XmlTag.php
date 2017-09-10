<?php
namespace fw;

class XmlTag {
	protected $_name;
	protected $_attr = array();
	protected $_content;
	protected $_meta = array();
	protected $_forceEnd = false;


	public function __construct($name, $content = null, $attr = array()){
		$this->_name = $name;
		$this->_content = $content;
		$this->_attr = $attr;
	}

	
	public function setTagName($name){
		$this->_name = $name;
		return $this;
	}
	public function getTagName(){
		return $this->_name;
	}
	/**
 * Pone el ID del Tag
 *
 * @param unknown_type $id
 * @return object Xmltag
 */

	public function setId($id){
		$this->setAttr('id', $id);
		return $this;
	}
	/**
 * Añade un atributo al tag
 *
 * @param unknown_type $name
 * @param unknown_type $value
 * @return object Xmltag
 */
	public function setAttr($name, $value){
		$this->_attr[$name] = $value;
		return $this;
	}
	/**
 * Elimina un atributo
 *
 * @param unknown_type $name
 * @return object Xmltag
 */
	protected function removeAttr($name){
		unset($this->_attr[$name]);
		return $this;
	}
	/**
 * Pone un atributo al valor
 *
 * @param array $attr
 * @return object Xmltag
 */public function setAttrs(array $attr){
	$this->_attr = $attr;
	return $this;
 }
 /**
 * Devuelve el valor del atributo
 *
 * @param unknown_type $name
 * @return bool or string
 */
public function getAttr($name){
 	if(isset($this->_attr[$name])){
 		return $this->_attr[$name];
 	}else{
 		return false;
 	}
 }
 /**
 * POne todo el meta al valor de array indicado
 *
 * @param array $meta
 * @return object Xmltag
 */
 protected function setMeta(array $meta){
 	$this->_meta = $meta;
 	return $this;
 }
 /**
 * Añade metadata value  al array de metas con nombre name solamente primer nivel
 *
 * @param unknown_type $name
 * @param unknown_type $value
 * @return unknown
 */
 protected function addMeta($name, $value){
 	$this->_meta[$name] = $value;
 	return $this;
 }
 /**
  * Return all array meta
  *
  * @param unknown_type $name
  * @param unknown_type $value
  * @return unknown
  */
 
 /**
 * Enter description here...
 *
 * @param unknown_type $value
 * @return unknown
 */
 protected function setContent($value){
 	$this->_content = $value;
 	return $this;
 }
 protected function addContent($value){
 	$this->_content .= $value;
 	return $this;
 }
 /**
 * Enter description here...
 *
 * @return unknown
 */
 protected function getContent(){
 	return (($this->_content && $this->_content != ' ')?$this->_content:false);
 }
 /**
 * Enter description here...
 *
 */
 protected function forceEnd(){$this->_forceEnd = true;}
 /**
 * Enter description here...
 *
 * @return unknown
 */


 public function start(){


 	$xml = '<' . $this->_name;
 	foreach ($this->_attr as $name => $value){
 		$xml .= ' '.$name.'="'.$value.'"';
 	}
 	if (isset($this->_content) || $this->_forceEnd){
 		$xml .= '>';
 	}else{
 		$xml .= '/>';
 	}
 	return $xml;

 }
 /**
 * Enter description here...
 *
 * @return unknown
 */
 public function end(){
 	return '</' . $this->_name . '>';
 }
 /**
 * Devuleve el string del objeto
 *
 * @return unknown
 */
 public function __toString(){
 	if(isset($this->_content) || $this->_forceEnd){
 		return $this->start().$this->_content.$this->end();
 	}else{
 		return $this->start();
 	}
 }
}
?>