<?php
namespace fw\Form;

\fw\Config::$INCLUDE_PATHS[] = FW_LIB_PATH."/third-party/";
use Respect\Validation\Exceptions\ValidationException;


class Field{
    protected $Label = null;
    private $Fields = array();
    private $Template;
    private $Text;
    private $Hint;

    private $ExtraCode = '';

    private $BypassValidation = false;

    private $Error = null;

    protected $Name;
    protected $InputData;
    protected $Data;

    function __construct($name, \fw\Data\Type $data = null){
        $this->Name = $name;
        $this->InputData = $this->getRawData();
        if(!$data){
            $this->Data = new \fw\Data\Type($this->InputData);
        }else{
            $this->Data = $data;
            $this->Data->setData($this->InputData);
        }
        if($this->Template == null){
            $temp = new \fw\Template(dirname(__FILE__).'/Templates/Bootstrap4Inline.tpl');
            $temp->setVar('field', $this);
            $this->setTemplate($temp);
        }

    }

    public function __clone()
    {
        foreach($this->Fields as $k=>$f){
            $this->Fields[$k] = clone $this->Fields[$k];
        }
        $this->Template = clone $this->Template;
        $this->Data = clone $this->Data;
    }

    public function setHint($text){
        $this->Hint = $text;
        return $this;
    }
    public function setPlaceholder($text){
        if($this->Fields[0])
            $this->Fields[0]->setPlaceholder($text);
        return $this;
    }
    public function getHint(){
        return $this->Hint;
    }

    public function isValid(){
        if($this->BypassValidation) return true;
        try {
            $this->Data->getValidator()->check($this->InputData);
            return true;
        } catch(ValidationException $exception) {
            return false;
        }
    }

    public function setInputData($data){
        $this->InputData = $data;
    }
    public function getInputData(){
        return $this->InputData;
    }
    public function validate(){
        if($this->BypassValidation) return true;
        try {
            $this->Data->getValidator()->check($this->InputData);
            return true;
        } catch(ValidationException $exception) {
            $exception->setName($this->getLabel());
            $this->Error = $exception->getMainMessage();
            return false;
        }
    }

    public function bypassValidation($val = true){
        $this->BypassValidation = $val;
        $this->Error = null;
    }

    public function addHtmlField(\fw\HtmlFormField $htmlformfield){
        $this->Fields[] = $htmlformfield;
        return $this;
    }

    public function setTemplate(\fw\Template $template){
        $this->Template = $template;
    }


    public function setError($error){
        $this->Error = $error;
        return $this;
    }
    public function getError(){
        return $this->Error;
    }


    public function getHtmlField($num = 0){
        if(isset($this->Fields[$num]))
            return $this->Fields[$num];
        return null;
    }
    public function getHtmlFields(){
        return $this->Fields;
    }
    public function getLabel(){return (is_null($this->Label)?$this->Name:$this->Label);}
    public function getText(){return $this->Text;}


    public function setLabel($label){$this->Label = $label; return $this;}


    public function getRawData(){
        $name = $this->getName();
        /*if(preg_match("/(.*)\[(.*)\]/", $name, $m)){
           $name = $m[1];
        };*/

        if(isset($_SERVER["CONTENT_TYPE"]) && strtolower($_SERVER["CONTENT_TYPE"]) == 'application/json'){
            $pedido = json_decode(file_get_contents('php://input'), true);
            return isset($pedido[$name]) ? $pedido[$name] : null;
        }else if(isset($_REQUEST[$name])){
            return $_REQUEST[$name];
        }else if(isset($_FILES[$name])){
            if(is_array($_FILES[$name]["tmp_name"])){
                $out = array();
                foreach($_FILES[$name]["tmp_name"] as $k=>$f){
                    if(!empty($f) && file_exists($f))
                        $out[$k] = file_get_contents($f);
                    else
                        $out[$k] = null;
                }
                return $out;
            }else{
                $f = $_FILES[$name]["tmp_name"];
                if(!empty($f) && file_exists($f)) return file_get_contents($f);
            }
        }
        return null;
    }

    public function setValue($value){
        $this->Data->setData($value);
        $this->getHtmlField()->setValue($value);
        return $this;
    }
    public function getValue(){
        return $this->Data->getData();
    }

    public function disabled($val = true){
        if($this->getHtmlField())
            $this->getHtmlField()->disabled($val);
        return $this;
    }


    public function getName(){
        return $this->Name;
    }
    public function setName($name){
        return $this->Name = $name;
        $this->getHtmlField()->setName($name);
    }
    /**
     * @return mixed
     */
    public function __toString(){
        if(is_null($this->Template)){
            return $this->getHtmlField()->__toString().$this->ExtraCode;
        }else{

            $this->Template->setVar("field", $this);
            return $this->Template->__toString().$this->ExtraCode;
        }
    }
}

