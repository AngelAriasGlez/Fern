<?php

namespace fw;
use Respect\Validation\Validator;

class FormFieldSelect extends FormField{
    private $Otpions;
    private $Multiple = false;
    function __construct($name, array $options = null, $selected = null)
    {
        $this->Validator = new Validator();
        $f = new HtmlFormField($name, HtmlFormField::SELECT);
        $f->setValue($options);
        $this->addHtmlField($f);
        $this->Otpions = $options;
        if($selected != null) $this->setValue($selected);
        parent::__construct($name);
    }
    public function setValue($value){
        $this->Value = $value;
        $this->getHtmlField()->selected($value);
        return $this;
    }

    public function multiple(){
        $this->getHtmlField()->setName($this->getName().'[]');
        $this->getHtmlField()->setAttr('multiple', 'multiple');
        return $this;
    }

}