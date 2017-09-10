<?php

namespace fw;
use Respect\Validation\Validator;

class FormFieldSubmit extends FormField{
    function __construct($text = null)
    {
        parent::__construct('submit');

        $this->Validator = new Validator();
        $this->addHtmlField(new HtmlFormField($this->getName(), HtmlFormField::SUBMIT));
        $this->addHtmlField(new HtmlFormField('', HtmlFormField::SUBMIT));
    }
    public function getLabel()
    {
        return '';
    }
}