<?php

namespace fw\Form\Field;
use Respect\Validation\Validator as v;

class PlainInput extends \fw\Form\Field{
    function __construct($name, \fw\Data\Type $type){
        parent::__construct($name, $type);

        $reflect = new \ReflectionClass($type);

        $this->addHtmlField(new \fw\HtmlFormField($name, $this->obtainHtmlType($reflect->getShortName())));
    }


    private function obtainHtmlType($type){
        switch($type){
            case 'LongString':
                return \fw\HtmlFormField::TEXTAREA;
            case 'Password':
                return \fw\HtmlFormField::PASSWORD;
            case 'Timestamp':
            case 'Date':
                return \fw\HtmlFormField::DATE;
            case 'Email':
                return \fw\HtmlFormField::EMAIL;
            case 'Number':
                return \fw\HtmlFormField::NUMBER;
            default:
                return \fw\HtmlFormField::TEXT;
        }
    }
}