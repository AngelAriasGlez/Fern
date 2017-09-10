<?php
/**
 * Created by PhpStorm.
 * User: angelariasgonzalez
 * Date: 11/2/18
 * Time: 22:01
 */

namespace fw\Data;

\fw\Config::$INCLUDE_PATHS[] = FW_LIB_PATH."/third-party/";
use Respect\Validation\Validator as v;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\ValidationException;

class Type
{
    protected $Data;
    protected $Validator;

    public function __construct($data = null)
    {
        $this->Data = $data;
        $this->Validator = new v();
    }

    public function __toString()
    {
        return (string)$this->Data;
    }
    public function setData($data){
        $this->Data = $data;
    }
    public function getData(){
        return $this->Data;
    }


    public function getFormFieldInstance($name){
        return new \fw\Form\Field\PlainInput($name, $this);
    }

    public function isValid(){
        try {
            $this->Validator->check($this->Data);
            return true;
        } catch(ValidationException $exception) {
            return false;
        }
    }
    public function getValidator(){
        return $this->Validator;
    }

    public function required(){
        $this->Validator = $this->Validator->notOptional();
        return $this;
    }

    /*public function __debugInfo() {
        return get_class($this).'::'.var_export($this->Data, true);
    }*/
}

