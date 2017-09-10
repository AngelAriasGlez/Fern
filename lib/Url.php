<?php
/**
 * Maneja la creacion de los <a href=""></a>
 *
 */
/*parse_url ("foo://username:password@example.com:8042/over/there/index.dtb;type=animal?name=ferret#nose");*/
namespace fw;
class Url {
	const HTTP = 'http';
	const HTTPS = 'https';

	protected $_scheme;
	protected $_user;
	protected $_pass;	
	protected $_host;
	protected $_port;
	protected $_path = array();
	protected $_query = array();
	protected $_fragment;
	
	protected $_showBase = true;
	protected $_showPath = true;
	protected $_showQuery = true;

	public function __construct(){}
	public static function create(){return new self();}


	public static function current(){
		global $_SERVER;
		$url = new self();
        $url->setScheme((isset($_SERVER['HTTPS']))?Url::HTTPS:Url::HTTP);
		
		if(isset($_SERVER['PHP_AUTH_USER'])){$url->setUser($_SERVER['PHP_AUTH_USER']);}
		if(isset($_SERVER['PHP_AUTH_PW'])){$url->setPassword($_SERVER['PHP_AUTH_PW']);}
		
		$url->setPort($_SERVER['SERVER_PORT']);
		$url->setHost($_SERVER['HTTP_HOST']);

        $u = parse_url($_SERVER['REQUEST_URI']);

        if(isset($u['path'])){
            $path = trim($u['path'], URI_SEP);
            if(!empty($path)){
                $url->setPath(explode(URI_SEP, $path));
            }
        }

        if(isset($u['query'])){$url->setQuery(self::decodeQuery($u['query']));}
        if(isset($u['fragment'])){$url->setFragment($u['fragment']);}
		return $url;
	}
	
	public function setUser ($user){$this->_user = $user; return $this;}
	public function setPassword ($pass){$this->_pass = $pass; return $this;}
	/**
	 * Pone el scheme
	 *
	 * @param string $scheme http,https,ftp,...
	 * @return fwHref
	 */
	public function setScheme ($scheme){$this->_scheme = $scheme; return $this;}
	/**
	 * Establece el Host
	 *
	 * @param string $host abc.com
	 * @return fwHref
	 */
	public function setHost ($host){$this->_host = $host; return $this;}
	/**
	 * Establce el puerto
	 *
	 * @param int $port 80,8080,443,...
	 * @return fwHref
	 */
	public function setPort ($port){$this->_port = $port; return $this;}
	/**
	 * Establece la ruta separada por /
	 *
	 * @param array $path /a/b/c
	 * @return fwHref
	 */
	public function setPath (array $path){$this->_path = $path; return $this;}
	/**
	 * Establce los parámetros ;type=asd
	 *
	 * @param array $params
	 * @return fwHref
	 */
	public function setQuery (array $query){$this->_query = $query; return $this;}
	/**
	 * Dirige a un label de la página "#"
	 *
	 * @param unknown_type $fragment
	 * @return unknown
	 */
	public function setFragment ($fragment){$this->_fragment = $fragment; return $this;}

	protected function buildBase(){
		$str = '';
		if(isset($this->_scheme)){
			$str = $this->_scheme.'://';
		}
		if(isset($this->_user)){
			$str .= $this->_user;
			if(isset($this->_pass)){
				$str .= ':'.$this->_pass;
			}
			$str .= '@';
		}
		if(isset($this->_host)){
			$str .= trim($this->_host, '/');
		}
		if (isset($this->_port) && ($this->_port != 80)){
			$str .= ':'.$this->_port;
		}
		return 	$str;
	}

	protected function buildPath(){
		if(count($this->_path) > 0){
			return URI_SEP.implode($this->_path, URI_SEP);
		}else{
			return '';
		}
	}
	
	public function __toString(){

		if($this->_showBase){	$str = $this->buildBase();}
		if($this->_showPath){	$str .= $this->buildPath();}

		/* -- Añade la Query -- */
		if($this->_showQuery && count($this->_query) > 0){
			$str .= '?'.self::encodeQuery($this->_query);
		}


		/* -- Añade el fragmento -- */
		if(isset($this->_fragment)){
			$str .= '#'.$this->_fragment;
		}
		return $str;
	}
	public function replaceQuery(array $mask){
		$this->_query = array_merge($this->_query, $mask);
		return $this;
	}

	public static function encodeQuery($query){
		/*$args = array();
		foreach($query as $n=>$v){
			$args[] = $n.(($v == '') ? '' : "={$v}");
		}
		$str = implode('&' , $args);*/
		return http_build_query($query);
	}

	public static function decodeQuery($str){
		parse_str($str, $output);
		return $output;
	}
	
	public function unsetQueryAndFragment(){
		$this->_query = NULL;
		$this->_fragment = NULL;
		return $this;
	}
	public function unsetQuerys(){
		$this->_query = NULL;
		return $this;
	}
	public function unsetQuery($name){
		if(isset($this->_query[$name])){
			unset($this->_query[$name]);
		}
		return $this;
	}
	public function unsetFragment(){
		$this->_fragment = NULL;
		return $this;
	}
	
	public function decode($uri){
		$u = parse_url($uri);
		if(isset($u['scheme'])){$this->_scheme = $u['scheme'];}
		if(isset($u['host'])){$this->_host = $u['host'];}
		if(isset($u['port'])){$this->_user = $u['port'];}
		if(isset($u['user'])){$this->_user = $u['user'];}
		if(isset($u['pass'])){$this->_pass = $u['pass'];}
		
		if(isset($u['path'])){
			$path = trim($u['path'], URI_SEP);
			if(!empty($path)){
				$this->_path = explode(URI_SEP, $path);
			}
		}
		if(isset($u['query'])){$this->_query = self::decodeQuery($u['query']);}
		if(isset($u['fragment'])){$this->_fragment = $u['fragment'];}
		
		return $this;
	}
	public function getScheme(){return $this->_scheme;}
	public function getPort(){return $this->_port;}
	public function getUser(){return $this->_user;}
	public function getPass(){return $this->_pass;}
	public function getHost(){return $this->_host;}
	public function getPath(){return $this->_path;}
	public function getQuery(){return $this->_path;}
	public function getFragment(){return $this->_path;}
}
?>