<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldNumber extends FormField{
    function __construct($name){
        parent::__construct($name);

        $this->Validator = v::optional(v::numeric());

        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::NUMBER));
    }

    public function max($max){
        $this->Validator->max($max);
        return $this;
    }
    public function min($min){
       $this->Validator->min($min);
        return $this;
    }
    public function between($min, $max){
        $this->Validator->between($min, $max);
        return $this;
    }
}