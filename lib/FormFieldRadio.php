<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldRadio extends FormField{
    private $Options;
    function __construct($name, $options){
        parent::__construct($name);

        $this->Validator = new v();

        $this->Options = $options;
        foreach($options as $k=>$o) {
            $f = new HtmlFormField($name, HtmlFormField::RADIO);
            $f->setValue($k);
            $f->setAttr('data-label', $o);
            $this->addHtmlField($f);
        }
    }
    public function getOptions(){
        return $this->Options;
    }

}