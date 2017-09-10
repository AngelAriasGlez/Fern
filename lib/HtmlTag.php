<?php
namespace fw;

class HtmlTag extends XmlTag {
	public function addClass($class){
		$c = parent::getAttr('class');
		parent::setAttr('class', $c.' '.$class);
		return $this;
	}
	public function removeClass($class){
		$c = parent::getAttr('class');
		$c = str_replace($class, '', $c);
		parent::setAttr('class', $c);
		return $this;
	}
	public function setStyle($style){
		parent::setAttr('style', $style);
		return $this;
	}	
}

?>