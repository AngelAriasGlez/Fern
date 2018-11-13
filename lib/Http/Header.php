<?php
/**
 * @revisado
 *
 */
namespace fw\Http;
class Header {
    private $Name;
    private $Content;

    public function __construct($name = null, $content = null)
    {
        $this->Name = $name;
        $this->Content = $content;
    }

    public function setName($name){
        $this->Name = $name;
    }
    public function setContent($content){
        $this->Content = $content;
    }
    public function getContent(){
        return $this->Content;
    }
    public function getName(){
        return $this->Name;
    }
    public function set($name, $content){
        $this->setContent($content);
        $this->setName($name);
    }

	private static $MIME_TYPES =    array(
        'txt'           => 'text/plain',
		'atom'          => 'application/atom+xml',
		'avi'           => 'video/x-msvideo',
		'bmp'           => 'image/bmp','compression',
		'exe'           => 'application/x-ms-dos-executable',
		'gif'           => 'image/gif',
		'htm'           => 'text/html',
		'html'          => 'text/html',
		'jar'           => 'application/x-jar',
		'jpg'           => 'image/jpeg',
		'png'           => 'image/png',
		'xhtml'         => 'application/xhtml+xml',
		'xml'           => 'text/xml',
		'zip'           => 'application/zip',
		'json'          => 'application/json',
        'pdf'           => 'application/pdf',
        'csv'           => 'text/csv',
	);

	
	public static function contentType($name, $encoding = NULL){
	    $obj = new self();
		$ct = $name;
		if(isset(self::$MIME_TYPES[$name])){
			$ct = self::$MIME_TYPES[$ct];
		}
			$ec = '';
			if(isset($encoding)){
				$ec .= '; charset='.$encoding;
			}
			$obj->set('Content-Type', $ct.$ec);
			return $obj;
	}
    public static function create($name, $content){
        $obj = new self();
        $obj->set($name, $content);
        return $obj;
    }
	public function send(){
        return header($this->getName().': '.$this->getContent());
    }
}
?>