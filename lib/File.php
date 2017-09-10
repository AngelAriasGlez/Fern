<?php
namespace fw;
class File{
	private $Path = '';
	private $MimeType;
    private $Size;
	public function __construct($filePath){
		$this->MimeType = mime_content_type($filePath);
		//exit($this->MimeType);
        $this->Size = filesize($filePath);
        $this->Path = $filePath;
	}
	public function getMimeType(){
		return $this->MimeType;
	}
    public function getSize(){
        return $this->Size;
    }

	public function send(){
        $g = fopen($this->Path, "rb");

        while (!feof($g)) {
            echo fread($g, 8192);
        }
        fclose($g);
    }

    public function toBase64(){
	    return 'data:' . $this->MimeType . ';charset=utf-8;base64,' . base64_encode($this->Data);
    }

}

?>