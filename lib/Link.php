<?php
/**
 * @todo aÃ±adir soporte e identificaciÃ³n de links que son https o humano requerido para aÃ±adir nofollow
 * @author jv
 *
 */
namespace fw;

class Link extends HtmlTag {
	protected $_href;
	protected $_hrefObj;

	public function __construct($href = null, $content = ' '){
		if ($href instanceof fwHref || $href instanceof Uri ){
			$hrefstr = $href->__toString();
			$this->_hrefObj = $href;
		}else{
			$hrefstr=$href;
		}
		$this->_href = $hrefstr;

		if(is_null($href)){
			parent::__construct('a', $content);
		}else{
			parent::__construct('a', $content, array('href' => $hrefstr));
		}
	}

	public function __toString(){
		if(!parent::getAttr('href')){
			if(isset($this->_hrefObj)){
				$this->_href = $this->_hrefObj->__toString();
			}
			parent::addAttr('href', $this->_href);
		}
		
/*		if (isset($this->_hrefObj->_controller)){
			if (isSetPath($GLOBALS['GB']['HUMAN_REQUIRED'],$this->_hrefObj->_controller)) {
					$this->setNofollow();
				}
		}*/
		return parent::__toString();
	}
/**
 * AÃ±ade nofollow al link
 * @return unknown_type
 */
	public function setNofollow(){
		parent::addAttr('rel', 'nofollow');
		return $this;
	}

/**
 * Pone el string de href a ...
 * @param $href
 * @return unknown_type
 */
	public function setHref($href){$this->_href = $href;
	return $this;
	}
/**
 * Devuelve el string de href
 * @return unknown_type
 */
	public function getHref(){
		return $this->_href;
	}
/**
 * Pone el titulo del link
 * @param $title
 * @return unknown_type
 */
	public function setTitle($title){
		parent::addAttr('title', $title);
		return $this;
	}
/**
 * Pone las teclas de cliente
 * @param $key
 * @return unknown_type
 */
	public function accessKey($key){
		parent::addAttr('accesskey', $key);
		return $this;
	}
/**
 * Pone el lenguaje del link 
 * @param $lang
 * @return unknown_type
 */
	public function langHref($lang){
		parent::addAttr('langhref', $lang);
		return $this;
	}
/**
 * (non-PHPdoc)
 * @see lib/XmlTag#setContent()
 */
	public function setContent($content){
		parent::setContent($content);
		return $this;
	}
/**
 * Crea un Link pasandole un Href objeto o string
 * @param $href
 * @param $content
 * @return unknown_type
 */
	public static function create($href, $content = ' '){
		return new self($href, $content);
	}

/**
 * 
 * @param $content
 * @param $opt
 * @return unknown_type
 */
	public static function internal($content, $opt = array()){
		if(empty($opt['route'])){$opt['route'] = Controller::getRoute();}
		if(empty($opt['action'])){$opt['action'] = '';}else{$opt['action'] .= URI_SEP;}
		if(empty($opt['args'])){$opt['args'] = array();}
		if(empty($opt['query'])){$opt['query'] = array();$qm='';}else{$qm='?';}
		//$array = array('href' => URL.$opt['route'].URI_SEP.$opt['action'].implode(URI_SEP, $opt['args']).$qm.implode('&amp;',$opt['query']));
		$array = array('href' => $opt['route'].URI_SEP.$opt['action'].implode(URI_SEP, $opt['args']).$qm.implode(Href::XML ,$opt['query']));
		return new XmlTag('a', $content, $array);
	}
/**
 * 
 * @param $str
 * @param $space
 * @param $symbol
 * @return unknown_type
 */
	public static function friendlyEncode($str, $space = '-', $symbol = '_'){
		/*
		 $str = mb_strtr($str, array('Ã¡' => 'a',Ã©' => 'e','Ã­' => 'i','Ã³' => 'o','Ãº' => 'u','Ã±' => 'n','Ã�' => 'A','Ã‰' => 'E','Ã�' => 'I','Ã“' => 'O','Ãš' => 'U','Ã‘' => 'N','-' => $symbol,' ' => $space	));
		 $str = preg_replace('&[^\w-]{1,3}&', $symbol, $str);
		 $str = strtolower($str);
		 return $str;*/
		//$str="Hola que tal estÃ¡s?";
		$url = strtolower($str);
		$url = str_ireplace (array('Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã±'), array('a', 'e', 'i', 'o', 'u', 'n'), $url);
		$url = str_ireplace (array(' ', '&', '\r\n', '\n', '+'), '-', $url);
		//$url = preg_replace (array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/'), array('', '-', ''), $url); // version con "<>"
		$url = preg_replace (array('/[^a-z0-9\-]/', '/[\-]+/', '/<[^>]*>/'), array('', '-', ''), $url);
		return $url; /* -------------------------------- WARN el decodificados no coincide con el codificador ------------- */
	}
	/**
	 * Decodifica una URL amigable, NON FUNCIONA CORRECTAMENTE
	 *
	 * @param string $str
	 * @return string
	 */
	public static  function friendlyDecode($str){
		$str = preg_replace('&-{1}&', ' ', $str);
		$str = preg_replace('&_{1}&', '%', $str);
		return $str;
	}
	/**
	 * Reemplaza los valores de la Query con los del nuevo array
	 *
	 * @param array $mask
	 * @param array $val
	 * @return array values
	 */
	public static function arrayQueryReplace (array $mask, $val = null){
		if(!is_array($val)) $val = fwDispatcher::getQuery();
		$values = array();
		foreach (array_merge($val,$mask) as $key => $value){
			if (!is_null($value))	$values[] = $key.'='.urlencode($value);
		}
		return $values;
	}
	/**
	 * Reemplaza la query actual con una mascara, devuelve la GET query
	 *
	 * @param array $mask
	 * @param array $val
	 * @return string "?a=b"
	 */
	public static function queryReplace(array $mask, $val = null){
		$request = fwDispatcher::getrequest();
		$values = self::arrayQueryReplace($mask,$val);
		return '?'.implode(Href::XML,$values);
	}
	/**
	 * Reemplaza los valores de los params con los del nuevo array
	 *
	 * @param array $mask
	 * @param array $val
	 * @return array values
	 */
	public static function arrayParamsReplace (array $mask, $val = null){
		if(!is_array($val)){
			$request = fwDispatcher::getRequest();
			$val = $request['params'];
		}
		$values = array();
		foreach (array_merge($val,$mask) as $key => $value){
			if (!is_null($value))	$values[] = $key.'='.urlencode($value);
		}
		return $values;
	}
	/**
	 * Reemplaza la query actual con una mascara, devuelve la GET query
	 *
	 * @param array $mask
	 * @param array $val
	 * @return string "?a=b"
	 */
	public static function paramsReplace(array $mask, $val = null){
		$values = self::arrayParamsReplace($mask,$val);
		return ';'.implode(Href::XML,$values);
	}
}

/*
 function mb_strtr($str, $from, $to = null){
 if(is_array($from)){
 foreach($from as $k=>$v){
 $str = str_replace($k, (($to)?$to:$v), $str);
 }
 }else{
 $str = str_replace($from, $to);
 }
 return $str;
 }*/

?>