<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldFile extends FormField{
    private $UploadName;
    function __construct($name)
    {
        parent::__construct($name);
        $this->UploadName = '__'.$name;
        $this->Validator = new v();
        $this->addHtmlField(new HtmlFormField($this->UploadName, HtmlFormField::FILE));
    }

    public function getRawData(){
        $name = $this->getName();

        if(isset($_FILES[$this->UploadName]) && !empty($_FILES[$this->UploadName]["tmp_name"])){

            $file = $_FILES[$this->UploadName]["tmp_name"];
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

}