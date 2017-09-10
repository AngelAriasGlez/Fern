<?php
namespace fw;
class LocalUrl extends Url{
	
	
	public static function create(){return new self();}

	public static function base(){
		$obj = new self();
		$obj->setCurrent();
		$obj->clear();
		return $obj;
	}
	public function c($controller){	return $this->setController($controller);}
	public function a($action){	return $this->setAction($action);}
	public function q(array $query){ return $this->setQuery($query);}



	public function setController($controller = NULL){
		//$this->unsetQueryAndFragment();
		//$this->unsetPathKey(self::ACTION_STR);
		//$controller = $controller.self::CONTROLLER_STR;

		//$key = $this->findPathKey(self::CONTROLLER_STR);
		
		$ca = $this->getControllerAndAction();
		$key = $this->findPathKey($ca[0]);
		
		if($key !== false){
			$this->_path[$key] = $controller;
		}else{
			$this->_path[] = $controller;
		}
		return $this;
	}
	private function unsetPath($str){
		$actionKey = $this->findPathKey($str);
		if($actionKey !== false){
			unset($this->_path[$actionKey]);
		}
	}
	
	private function findPathKey($str){
		if(isset($this->_path)){
			foreach($this->_path as $k=>$v){
				if(strpos($v, $str) !== false){
					return $k;
				}
			}
		}
		return false;
	}
	/*public function getPath($str){
		$key = $this->findPathKey($str);
		if($key !== false){
			return rtrim($this->_path[$key], $str);
		}
		return false;
	}*/
	public function getController(){
		$ca = $this->getControllerAndAction();
		return $ca[0];
	}
	public function getAction(){		
		$ca = $this->getControllerAndAction();
		return $ca[1];
	}
	
	public function unsetAction(){
		$ca = $this->getControllerAndAction();
		$this->unsetPath($ca[1]);
	}
	public function unsetController(){
		$ca = $this->getControllerAndAction();
		$this->unsetPath($ca[0]);
	}	
	public function setAction($action){
		//$action = $action.self::ACTION_STR;
		//$this->unsetQueryAndFragment();
		$path = $this->_path;
		$ca = $this->getControllerAndAction();
		$ckey = $this->findPathKey($ca[0]);
		if(!$ckey) $path[] = $ca[0];
	
		$akey = $this->findPathKey($ca[1]);

		if($akey !== false){
			$path[$akey] = $action;
		}else{
			$path[] = $action;
		}
		$this->_path = $path;
		return $this;
	}
	

	public function toLink(){
		$str = $this->__toString();
		return new Link($this->__toString(), $str);
	}
	
	public function __toString(){
		if(isset(self::$_baseRoute)){
			$path = $this->getPath();

			$this->setPath(array_merge(self::$_baseRoute, $path));
		}
		return parent::__toString();
	}
	
	private function clear(){
		$this->unsetAction();
		$this->unsetController();
		$this->unsetQueryAndFragment();
		return $this;
	}
	
	private function getRealPath(){
		$path = $this->_path;
		$base = explode(URI_SEP, trim(BASE_URI, URI_SEP));
		foreach($path as $pk=>$pv){
			if(in_array($pv, $base)){
				unset($path[$pk]);
			}
		}
		return array_values($path);		
	}
	
	private function getControllerAndAction(){
		$path = $this->getRealPath();
		$countPath = count($path);
		if($countPath == 1){
			$controllerName = $path[0];
			$actionName = Router::MVC_DEFAULT_ACTION;
		}elseif($countPath >= 2){
			$controllerName = $path[0];
			$actionName = $path[1];
		}else{
			$controllerName = Router::MVC_DEFAULT_CONTROLLER;
			$actionName = Router::MVC_DEFAULT_ACTION;
		}
		return array($controllerName, $actionName);
	}
	
	/*public static function spath($path){
		$l = LocalUri::base();
		$p = explode(URI_SEP, trim($path, URI_SEP));
		if(isset($p[0])) $l->setController($p[0]);
		if(isset($p[1])) $l->setAction($p[1]);
		return $l;
	}*/
	
	public static function replace($action, $controller = NULL){
		$l = LocalUri::current();
		$l->setAction($action);
		if(!is_null($controller)) $l->setController($controller);
		return $l;
	}
	/*public function replaceArgs($args){
		$l = LocalUri::current();
		$l->setAction($action);
		if(!is_null($controller)) $l->setController($controller);
		return $l;
	}*/
	public static function set($action, $controller = NULL){
		$l = LocalUri::base();
		$l->setAction($action);
		if(!is_null($controller)) $l->setController($controller);
		return $l;
	}

}
