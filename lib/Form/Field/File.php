<?php

namespace fw\Form\Field;
use Respect\Validation\Validator as v;

class File extends \fw\Form\Field{
    private $UploadName;
    private $FileInput;
    private $ReadFile;
    function __construct($name, $type, $readfile = false)
    {
        $this->ReadFile = $readfile;
        $this->UploadName = '__'.$name;
        parent::__construct($name, $type);
        $this->Validator = new v();
        $this->FileInput = new \fw\HtmlFormField($this->UploadName, \fw\HtmlFormField::FILE);
        $this->addHtmlField($this->FileInput);

    }

    public function getRawData(){
        $name = $this->getName();

        if(isset($_FILES[$this->UploadName]) && !empty($_FILES[$this->UploadName]["tmp_name"])){

            $file = $_FILES[$this->UploadName]["tmp_name"];
            if(!$this->ReadFile){
                return $file;
            }
            if(!empty($file) && file_exists($file)) {
                return file_get_contents($file);
            }




        }else if(isset($_REQUEST[$name])){
            //if($_REQUEST[$name] == 'NULL') return null;
            return $_REQUEST[$name];
        }else{

        }

        return null;
    }
    public function multiple(){
        $this->FileInput->setAttr('multiple', 'multiple');
        return $this;
    }

}