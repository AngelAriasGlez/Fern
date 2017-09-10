<?php
namespace fw\Form;


class Group implements \ArrayAccess,\Countable,\Iterator{

    private $Fields = array();
    private $Name;
    

    public function __construct($name = null, $fields = null)
    {
        
        $this->setName($name);
        $this->add($fields);
    }
    public function setName($name){
    $this->Name = $name;
}
    public function getName(){
        return $this->Name;
    }

    public function add($fs){
        if(is_array($fs)){
            foreach($fs as $f){
                if($f instanceof Field){
                    $this->Fields[] = $f;
                }
            }
        }else if($fs instanceof Field){
            $this->Fields[] = $fs;
        }
    }


    public function count(){
    return count($this->Fields);
}

    public function rewind(){
    reset($this->Fields);
}

    public function current(){
    return current($this->Fields);
}

    public function key(){
    return key($this->Fields);
}

    public function next(){
    return next($this->Fields);
}

    public function valid(){
    $key = key($this->Fields);
    return ($key !== NULL && $key !== FALSE);
}
    public function first(){
    if(isset($this->Fields[0])) {
        return $this->Fields[0];
    }else {
        return null;
    }
}

    public function offsetExists($offset){return isset($this->Fields[$offset]);}
    public function offsetGet($offset){return $this->Fields[$offset];}
    public function offsetSet($offset,$value){
        if($value instanceof Field) {
            $this->Fields[$value->getName()] = $value;
        }
    }
    public function offsetUnset($offset){unset($this->Field[$offset]);}

    public function toArray(array $fields = null)
    {
        return $this->Fields;
    }

    public function findField($name)
    {
        foreach($this->Fields as $v){
            if($v instanceof Field && $v->getName() == $name) {
                return $v;
            }else if($v instanceof Group){
                $f = $v->findField($name);
                if($f !== null) return $f;
            }
        }
        return null;
    }
    public function get($name)
    {
        if(isset($this->Fields[$name]) ) return $this->Fields[$name];
        return null;
    }


    public function filterFields($fieldsNameRegex){
        $group = new self;
        foreach($this->Fields as $n=>$f){
            if(preg_match('/'.$fieldsNameRegex.'/', $n) == 1)  $group->add($f);
        }
        return $group;
    }


    public function __toString(){
        $out = "";
        foreach($this->Fields as $n=>$f){
            $out .= $f->__toString();
        }
        return $out;
    }
}

