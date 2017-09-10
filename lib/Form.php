<?php
namespace fw;

use fw\Form\Field;

class Form extends HtmlTag {
	public static $hasInstance = false;
	const POST = 'post';
	const GET = 'get';
	const AJAX = 'ajax';

	const MULTIPART_DATA = 'multipart/form-data';
	const URL_ENCODED = 'application/x-www-form-urlencoded';

	private $_method;
	private $_action;
	private $_enctype = self::MULTIPART_DATA;
	private $_charset = DEF_CHARSET;
	private $_contenttype;
	private $_id;

	private $_error;
	
	private $Groups = array();

	private $Template = null;

	private $Hash;


    private $Submitted = false;
    private $BindedObj;



	public function __construct($id, $method = self::POST){
		$this->_method = $method;
		
		parent::__construct('form');
		if(self::$hasInstance == false){
			self::$hasInstance = true;
		}
		if(empty($id)){
			throw new Exception("Empty form id.");
		}
		parent::setAttr('id', $id);
		$this->_id = $id;

		parent::setContent(' ');

        $rand = $this->_id . "-" .microtime();
        $this->Hash = sha1($rand);



        if (isset($_SESSION['fw\Form'][$this->getId()]) && isset($_REQUEST[$this->getId() . '_hash'])) {
            if ($_REQUEST[$this->getId() . '_hash'] == $_SESSION['fw\Form'][$this->getId()]) {
                $this->Submitted = true;
            }
        }
	}
	public function __destruct()
    {
        if(empty($_SESSION[__CLASS__])){
            $_SESSION[__CLASS__] = array();
        }
        $_SESSION[__CLASS__][$this->_id] = $this->Hash;
    }

    public function forceSubmit(){
        $this->Submitted = true;
    }


    public function start(){
		if($this->_method === self::AJAX){
			
		}else{
			parent::setAttr('method', $this->_method);
		}

		if(empty($this->_action)){$this->_action = LocalUrl::create()->__toString();}
		parent::setAttr('action', $this->_action);
		if(isset($this->_enctype)){parent::setAttr('enctype', $this->_enctype);}
		if(isset($this->_charset)){parent::setAttr('accept-charset', $this->_charset);}
		if(isset($this->_contenttype)){parent::setAttr('accept', $this->_contenttype);}


		$hf = HtmlFormField::create($this->getId().'_hash', HtmlFormField::HIDDEN)->setValue($this->Hash);
		/*foreach($this->_hiddenFields as $hidden){
			$hf .= $hidden->__toString();
		}*/
		return parent::start().$hf;
	}

	/**
	 * Retorna los ultimos valores almacenados para este formulario
	 * @param $form
	 * @return unknown_type
	 */



	/**
	 * Añade un campo a la forma
	 *
	 * @param xHtmlField $field
	 * @param unknown_type $fieldGroup
	 * @return unknown
	 */
	public function addGroup(\fw\Form\Group $fs){
		$this->Groups[] = $fs;
		return $this;
	}






    public function validateField($fieldOrName)
    {
        if (empty($fieldOrName) /*|| !$this->Submitted*/) {
            return false;
        }

        if(is_string($fieldOrName)){
            $field = $this->findField($fieldOrName);
        }else if($fieldOrName instanceof \fw\Form\Field){
            $field = $fieldOrName;
        }
        if($field == null){
            throw new Exception('Bad field '.$fieldOrName);
        }
        $name = $field->getName();
        if(empty($name))  return false;

        /*if(isset($this->BindedObj) && property_exists($this->BindedObj, $name)){
            $val = $this->BindedObj->getBindedField($name ,$field);
        }else{
            $val = $field->getRawData();
        }


        //$val = $field->getRawData();
        $field->setInputData($val);*/
        $result = $field->validate();
        //var_dump($name, substr($val, 0,10), $result);

        return $result === true;
    }
    public function validate()
    {
        $res = true;
        foreach ($this->getAllFields() as $name => $field) {
            if(!$this->validateField($field)) $res = false;
            //var_dump($name.' '.$res);
        }
        return $res;
    }
    public function validateGroup($groupName)
    {
        $fields = $this->getGroup($groupName);

        $res = true;
        foreach($fields as $k=>$f){
            if(!$this->validateField($f)) $res = false;
        }
        return $res;
    }

    public function reset()
    {
        foreach ($this->getAllFields() as $name => $field) {
               $field->setValue(null);
        }
    }
    public function getAllFields(){
	    $out = array();
        foreach ($this->getGroups() as $g) {
            foreach ($g as $field) {
                $out[] = $field;
            }
        }
        return $out;
    }


    public function bind(\fw\Form\BindableObject &$obj){
        $this->BindedObj = &$obj;
        foreach ($this->getAllFields() as $field) {
            $name = $field->getName();
            $initval = $this->BindedObj->getBinded($name, $field);
            if ($this->Submitted) {
                $val = $field->getInputData();
                if ($val === null) {
                    $field->setValue($initval);
                } else {
                    //TODO: Need filter
                    if (is_array($val) && (is_string($initval) && ($iv = json_decode($initval, true)))) {

                        //var_dump($iv);
                        $val = array_replace($iv, $val);
                    }
                    $resultval = $this->BindedObj->setBinded($name, $val, $field);
                        //var_dump("$name '$val' '$resultval'");
                    if($field->isValid()) {
                        $field->setValue($resultval);
                    }else {
                        $field->setValue($initval);
                    }
                }
            }else{
                $field->setValue($initval);
            }
        }
    }

    public function getErrors()
    {
        $out = array();
        foreach ($this->getAllFields() as $name => $field) {
            $e = $field->getError();
            if($e) $out[] = $e;

        }
        return $out;
    }
    public function hasErrors(){return count($this->getErrors()) > 0;}

    public function getValue($fieldName)
    {
        if($field = $this->findField($fieldName))
            return $field->getValue();
        return null;
    }
    public function getValues()
    {
        $out = array();
        foreach ($this->getAllFields() as $name => $field) {
            $out[$field->getName()] = $field->getValue();
        }
        return $out;
    }
    public function setError($fieldName, $text)
    {
        if($field = $this->findField($fieldName))
            return $field->setError($text);
    }

    public function isSubmitted(){return $this->Submitted;}


    /*
    public function getErrors(){return $this->Errors;}
    public function hasErrors(){return count($this->Errors) > 0;}
    public function hasFieldErrors($fieldName){return isset($this->Errors[$fieldName]);}
    public function hasFieldValue($fieldName){return isset($this->Values[$fieldName]);}
    public function getError($fieldName){return (isset($this->Errors[$fieldName])) ? $this->Errors[$fieldName] : null;}*/





	/*public function getErrorMessage(){return $this->_error;}



	public function setErrorMessage($error){$this->_error = $error; return $this;}


	public function getSubmitButton($text = 'Enviar'){
		return fwHtmlField::create($this->getId().'_submit')->setType('submit')->setValue($text);
	}




	public function createField($fieldId, $params = array(), $fieldtype = fwHtmlField::INPUT){
		$field = fwHtmlField::create($fieldId, $params, $fieldtype);
		$this->addField($field);
		return $field;
	}


	public function getFieldsGroup($groupName){
	    foreach($this->Fields as $fs){
	        if($fs instanceof FormFieldGroup){
	            if($fs->getName() == $groupName){
	                return $groupName;
                }
            }
        }
        return null;
	}*/


    /*

	public function delField($name){unset($this->_fields[$name]);}*/





	public function getGroup($nameOrIndex){
	    $group = null;
	    if(is_int($nameOrIndex)){
            $group = array_values($this->Groups)[$nameOrIndex];
        }else{
	        foreach($this->Groups as $g){
	            if($g->getName() == $nameOrIndex){
	                $group = $g;
	                break;
                }
            }
        }

        if (isset($group) && !is_null($this->Template))
        foreach($group as $f) {
            $f->setTemplate($this->Template);
        }

        return $group;
	}

    /**
     * @param $name
     * @return \fw\Form\Field
     */
    public function findField($name){
	    $f = null;
	    foreach($this->Groups as $g){
	        $f = $g->findField($name);
            if($f !== null) break;
        }

        if (!is_null($this->Template) && $f) {
	        $f->setTemplate($this->Template);
        }

        return $f;
    }



    public function addSubmitButton($text = null){
	    $submit = new \Form\Group('submit', (new FormFieldSubmit())->setLabel($text));
        $this->addGroup($submit);
        return $this;
    }




	/**
	 * Trae todo el array de fields
	 *
	 * @return unknown
	 */
	public function getGroups(){return $this->Groups;}



	/**
	 * Retorna el id de la forma
	 *
	 * @return unknown
	 */
	public function getId(){return $this->_id;}






	/**
	 * Pone el metodo de envia de la forma a valor
	 *
	 * @param unknown_type $method
	 */
	public function setMethod($method){$this->_method = $method;}


	
	public function getMethod(){return $this->_method;}
	

	/**
	 * Pone la URL de la accion
	 *
	 * @param unknown_type $action
	 */
	public function setAction($action){
		if($action instanceof Href){
			$action = $action->disableSid()->__toString();
		}
		$this->_action = $action;
		return $this;
	}
	public function getAction(){
		return $this->_action;
	}


	/**
	 * Pone el tipo de codificacion
	 *
	 * @param unknown_type $type
	 */
	public function setEnctype($type){$this->_enctype = $type;}


	public function setTemplate(Template $template){
        $template->setVar('form', $this);
        //$template->setVar('handler',  $this->Handler);
	    $this->Template = $template;
    }



	public function __toString()
    {
        $out = $this->start();
        foreach($this->Groups as $g) {
            foreach($g as $f) {
                if (!is_null($this->Template)) $f->setTemplate($this->Template);
                $out .= $f->__toString();
            }
        }
        $out .= $this->end();
        return $out;
    }


}
?>