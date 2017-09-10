<?php
namespace fw;
class Media{
	private $Data = '';
	private $MimeType;
	public function __construct($data, $mimeType){
		$this->MimeType = $mimeType;
		$this->Data = $data;
	}
	
	public function __toString(){
	    //return $this->toHtml()->__toString();
		return $this->Data;
	}
	public function getMimeType(){
		return $this->MimeType;
		
	}


	public function toHtml(){
	    $arr = explode('/', $this->MimeType);
	    if($arr[0] == 'image') {
            $img = new HtmlTag('img', null, array('src' => $this->toBase64()));
            return $img;
        }else{
	        return null;
        }
    }
    public function toBase64(){
	    return 'data:' . $this->MimeType . ';charset=utf-8;base64,' . base64_encode($this->Data);
    }
	
	/*private $_src;
	public function __construct($href, $alt=''){
		$this->_src = $href;
		if($href instanceof Href){
			$href = $href->__toString();
		}else{
			if(strpos($href, ':') === false){
				$href = IMG_URL.$href;
			}
		}
		$array = array('src' => $href, 'alt' => $alt, 'title' => $alt);
		parent::__construct('img', null, $array);
	}
	public function getSrc(){
		return $this->_src;
	}

	public function setWidth($width){
		parent::addAttr('width', $width);
		return $this;
	}
	public function setHeight($height){
		parent::addAttr('height', $height);
		return $this;
	}
	public function setSize($size){
		$this->setWidth($size);
		$this->setHeight($size);
		return $this;
	}
	public static function create($src, $alt = ''){
		return new self($src, $alt);
	}*/
}

?>