<?php

namespace fw;
use Respect\Validation\Validator;

class FormFieldText extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = new Validator();
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::TEXT));
    }
}