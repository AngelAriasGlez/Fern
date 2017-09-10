<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldTime extends FormField{
    function __construct($name)
    {
        parent::__construct($name);

        $this->Validator = v::optional(v::date("H:i"));
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::TIME));
    }
}