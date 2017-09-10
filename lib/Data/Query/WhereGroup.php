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
    public function where(Where $where){
        $this->Data[] = $where;
        return $this;
    }

    public function getBindedValues(){
        $out = array();
         foreach($this->Data as $d){
             foreach($d->getBindedValues() as $v){
                 $out;
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
            default:
                trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }
    }

    public function __toString(){
        return implode(' ', $this->Data);
    }
}
function WhereGroup(){
    return WhereGroup::build();
}
?>