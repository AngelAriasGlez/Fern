<?php
namespace fw;

use \fw\HtmlTag;

class HtmlFormField extends HtmlTag {
	const SELECT = 'select';
	const INPUT = 'input';
	const TEXTAREA = 'textarea';
	
	const HIDDEN = 'hidden';
	const TEXT = 'text';
	const RADIO = 'radio';	
	const CHECKBOX = 'checkbox';
	const PASSWORD = 'password';
	const SUBMIT = 'submit';
	const RESET = 'reset';
    const FILE = 'file';

	/*HTML5*/
	const NUMBER = 'number';
	const DATE = 'date';
	const EMAIL = 'email';
	const SEARCH = 'search';
	const TEL = 'tel';
	const URL = 'url';
	const TIME = 'time';
	const WEEK = 'week';
	const DATETIME_LOCAL = 'datetime-local';
	const DATETIME = 'datetime';
	const COLOR = 'color';
	const MONTH = 'month';
	const RANGE = 'month';

	
	private $Value;

	private $Rules;

	public static function create($name, $type = self::INPUT){
		return new self($name, $type);
	}
	public function __construct($name = null, $type = self::INPUT){

		if($type !== self::SELECT && $type !== self::TEXTAREA){
			$tag = 'input';
		}else{
			$tag = $type;			
		}
		
		parent::__construct($tag);
		
		parent::setAttr('name', $name);
		parent::setAttr('id', $name);
		
		if($type !== self::SELECT && $type !== self::TEXTAREA){
			$this->setType($type);
		}

	}
    public function setName($name){
        return parent::setAttr('name', $name);
    }
	public function getName(){
	   return parent::getAttr('name');
    }
	
	public function setRules($rules){
		$this->Rules = $rules;
		return $this;
	}
	public function getRules(){
		return $this->Rules;
	}

	public function getId(){return parent::getAttr('id');}


	public function setType($type){
		if(parent::getTagName() !== self::INPUT){
			throw new \Exception('setType only valid for input');
		}
		if($type == self::INPUT) $type = self::TEXT;
		
		parent::setAttr('type', $type);return $this;}
	
	public function setPlaceholder($ph){
		parent::setAttr('placeholder', $ph);return $this;
	}



	public function setValue($value){
		if(!(@is_array($value) || is_null($value)) && parent::getTagName() === self::SELECT){
			//throw new Exception('SELECT value must be array '.parent::getAttr('name'), 500);
		}
		$this->Value = $value;
		return $this;
	}
    public function getValue(){
        return $this->Value;
    }

	public function disabled($val=true){
		if ($val){
			parent::setAttr('disabled', 'disabled');
		}
		return $this;
	}

	public function checked($value = true){
		if(parent::getAttr('type') == 'radio' || parent::getAttr('type') == 'checkbox' ){
			if($value === true){
				parent::setAttr('checked', 'checked');
			}else{
				parent::removeAttr('checked');
			}
		}else{
			//throw new Exception('Bad radio '.parent::getAttr('name').'" type must be radio or checkbox.', 500);
		}
		return $this;
	}
	public function selected($sel){
		if(parent::getTagName() === self::SELECT){
            //var_dump($sel);gasogal
			parent::setAttr('selected', $sel);
		}else{
			//throw new Exception(500,"Bad select",false,'"'.parent::getAttr('name').'" type must be SELECT.');
		}
		return $this;
	}
	

	public function __toString(){
		switch (parent::getTagName()){
			case self::INPUT:
				//select in old css
				//if(!parent::getAttr('type')){parent::setAttr('type', 'text');}
				//parent::addClass(parent::getAttr('type'));
                if($this->Value !== null) parent::setAttr('value', $this->Value);
				/*parent::setAttr('tabindex', $this->_attr);*/
				break;
			case self::SELECT:

				$sel = parent::getAttr('selected');
				parent::removeAttr('selected');
				$fv = '';
				if(count($this->Value))
				foreach($this->Value as $name=>$val){
					$attr=array();
					if($name == $sel || (is_array($sel) && array_search($name, $sel) !== false)){
						$attr['selected'] = 'selected';
					}
					$attr['value'] = $name;
					$option = new XmlTag('option', $val, $attr);
					$fv .= $option->__toString()."\r\n";
				}
				parent::setContent($fv);
				break;
			case self::TEXTAREA:
				parent::setContent($this->Value);
				parent::forceEnd();
				break;
		}

		return parent::__toString();
	}
}
