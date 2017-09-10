<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldEmail extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = v::optional(v::email());
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::EMAIL));
    }
}