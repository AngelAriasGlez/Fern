<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldMultiPhoto extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = new v();
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::TEXT));
    }

    public function validate()
    {
        $val = array_merge($this->InputData['new'], (array)$this->Value);

        if(count($val) - count($this->InputData['delete']) <= 0) {
            parent::setInputData(null);
        }
        return parent::validate();

    }

    public function getRawData(){
        $name = $this->getName();


        $photos = array('new'=>array(), 'delete'=>array());
        if(isset($_FILES[$name]["tmp_name"])) {
                foreach ($_FILES[$name]["tmp_name"] as $k => $n) {
                    if (isset($n) && $n != '' && file_exists($n)) {
                        $photos['new'][$k] = file_get_contents($n);
                        //$photos['new'][$k] = $n;
                    }
                }
        }

        $notModified = 0;
        if(isset($_REQUEST[$name])){
            if(is_array($_REQUEST[$name])) {
                foreach ($_REQUEST[$name] as $k => $v) {
                    if ($v == '') {
                        $photos['delete'][] = $k;
                    } else {
                        $notModified++;
                    }
                }
            }else{
                if ($v == '') {
                    $photos['delete'][] = $k;
                } else {
                    $notModified++;
                }
            }
        }
        //var_dump($photos);
        //return ((count($photos['new']) + $notModified) > 0)?$photos:null;
        return $photos;

    }
}