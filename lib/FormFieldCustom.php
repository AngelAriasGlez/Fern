<?php

namespace fw;

class FormFieldCustom extends FormField{
    function __construct($name,Validator $validator, $htmlformfieldtype)
    {
        parent::__construct($name);

        $this->Validator = $validator;
        $this->addHtmlField(new HtmlFormField($name, $htmlformfieldtype));
    }
}