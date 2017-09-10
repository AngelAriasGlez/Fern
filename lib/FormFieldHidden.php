<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldHidden extends FormField{
    function __construct($name, $value = null)
    {
        parent::__construct($name);

        $this->Validator = new v();
        $field = new HtmlFormField($name, HtmlFormField::HIDDEN);
        $field->setValue($value);
        $this->addHtmlField($field);
    }
}