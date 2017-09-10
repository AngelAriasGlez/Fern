<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldUrl extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = v::Url();
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::URL));
    }
}