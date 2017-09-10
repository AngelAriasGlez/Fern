<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldCheckbox extends FormField{
    function __construct($name){
        parent::__construct($name);

        $this->Validator = new v();

        $f = new HtmlFormField($name, HtmlFormField::CHECKBOX);
        $f->setValue('true');
        $this->addHtmlField($f);

    }

}