<?php
namespace fw\Data\Query;
class WhereGroup{
    private $Data = array();
    public static function build(){
        return new self();
    }

    public function _and(){
        $this->Data[] = "AND";
        return $this;
    }
    public function _or(){
        $this->Data[] = "OR";
        return $this;
    }
    public function _not(){
        $this->Data[] = "NOT";
        return $this;
    }
	private function isOperator($d){
		return $d == 'AND' || $d == 'OR' || $d == "NOT";
	}
	public function lastIsOperator(){
		return $this->isOperator(end($this->Data));
	}
	
    public function where($where){
        $this->Data[] = $where;
        return $this;
    }

    public function getBindedValues(){
        $out = [];
         foreach($this->Data as $d){
			 if($d instanceof Where || $d instanceof WhereGroup){
				$out = array_merge ($out, $d->getBindedValues());
			 }
         }
         return $out;
    }

    public function __call($name, $arguments){
        switch($name){
            case 'and':
                return $this->_and();
            case 'or':
                return $this->_or();
            case 'not':
                return $this->_not();
            default:
                trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }
    }

    public function __toString(){
        return '('.implode(' ', $this->Data).')';
    }
	public function count(){
        return count(array_filter($this->Data, function($d){return !$this->isOperator($d);}));
    }
}
function WhereGroup(){
    return WhereGroup::build();
}
?>