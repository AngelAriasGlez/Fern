<?php
namespace fw\Data\Query;
class Where{
    private $Query = array();
    private $Values = array();
    public static function build(){
        return new self();
    }
    public function __construct(){
    }
    public function equals($name, $value){
        return $this->common($name, $value, '=');
    }
    public function notEquals($name, $value){
        return $this->common($name, $value, '!=');
    }
    public function greater($name, $value){
        return $this->common($name, $value, '>');
    }
    public function greaterOrEqual($name, $value){
        return $this->common($name, $value, '>=');
    }
    public function less($name, $value){
        return $this->common($name, $value, '<');
    }
    public function lessOrEqual($name, $value){
        return $this->common($name, $value, '<=');
    }
    public function in($name, $value){
        return $this->common($name, $value, 'IN');
    }
    public function notIn($name, $value){
        return $this->common($name, $value, 'NOT IN');
    }
    public function notNull($name){
        return $this->literal($name, 'NULL', 'IS NOT');
    }
    public function isNull($name){
        return $this->literal($name, 'NULL', 'IS');
    }
    public function like($name, $value){
        return $this->common($name, $value, 'LIKE');
    }
	public function notLike($name, $value){
        return $this->common($name, $value, 'NOT LIKE');
    }
    /*

    public function between($column, $a, $b);
    public function notBetween($column, $a, $b);
    public function isNull($column);
    public function isNotNull($column);
    public function exists(Select $select);
    public function notExists(Select $select);
    public function addBitClause($column, $value);
    public function asLiteral($literal);
    */
    private function literal($name, $value, $comp){
        $this->Query[] = "$name $comp $value";
        return $this;
    }
    private function common($name, $value, $comp){
        $this->Query[] = "$name $comp ?";
        $this->Values[] = $value;
        return $this;
    }
    public function _and(){
        $this->Query[] = "AND";
        return $this;
    }
    public function _or(){
        $this->Query[] = "OR";
        return $this;
    }
	public function _not(){
        $this->Query[] = "NOT";
        return $this;
    }
    public function getBindedValues(){
        return $this->Values;
    }

    public function __toString(){
        return '('.implode(' ', $this->Query).')';
    }

    public function __call($name, $arguments){
        switch($name){
            case 'and':
                return $this->_and();
            case 'or':
                return $this->_or();
	        case 'not':
                return $this->_or();
            default:
                trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }
    }

    public function count(){
        return count($this->Query);
    }
   /*public static function __callStatic($name, $arguments){
        $obj = new self();
        if(is_callable(array($obj, $name))){
            return $obj->$name();
        }
        trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
    }*/

}
function Where(){
    return Where::build();
}
?>