<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldPassword extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = v::optional(v::noWhitespace()->length(8, 30));
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::PASSWORD));
    }
}