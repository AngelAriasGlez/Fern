<?php
namespace fw\Data;
class Record implements \ArrayAccess, \fw\Form\BindableObject{
	private $HydrateData = array();


	public function __construct(){

	}

	public function hydrate($data){
        foreach($data as $k=>$d){
            $this->$k = $d;
        }
        $this->HydrateData = $data;
        return $this;
    }

	public function set($name, $value){
		//if(!parent::isColumn($name)){
		//if($name != 'Id'){
			//throw new Exception('Column '.$name.' does not exist in table definition '.get_class($this));
		//}else{
			$this->$name = $value;
		//}
		return $this;
	}
	
	public function load($name){

		$repo = $this->getRepository();
		$struct = $repo->getStructure();
		if(empty($struct[$name])){
			throw new Exception("Try to load blanck field `$name` on `".get_class($this)."`");
		}
		if(array_key_exists('foreing', $struct[$name])){
			$repo = call_user_func([$struct[$name]['foreing'][0], 'getRepository']);
			return $repo->findByPk($this->$name);
		}
		return NULL;
	}

	public function get($name){
		/*if(!parent::isColumn($name)){
			throw new Exception('Column '.$name.' does not exist in table '.get_class($this));
		}*/
		if(isset($this->$name) && $this->$name != ''){
			if(is_numeric($this->$name) && \fw\isDataClass($name)){
				$reponame = $name.'Repository';
				$this->$name = $reponame::findByPk($this->$name);
			}

			return $this->$name;
		}
		return NULL;
	}
	
	public function contains($name){
		return isset($this->$name);
	}
	public function isNull($name){
		if (isset($this->$name)) return is_null($this->$name);
		return true;
	}
	public function isModified($name){
		return $this->$name != $this->HydrateData[$name];
	}
    public function getInitData($name){
        return $this->HydrateData[$name];
    }
	public function getModified(){
		$c = array();
		foreach(get_object_vars($this) as $k=>$v){
			if($k != 'HydrateData' && (empty($this->HydrateData[$k]) || $this->isModified($k))){
				$c[$k] = true;
			}
		}
		return $c;
	}
	public function remove($name){
		unset($this->$name);
	}

	public function save(){
		$res = $this->getRepository()->save($this);
		if(isset($res[0])) return $res[0];
		return null;
	}
	
	
	public function delete(){
		return $this->getRepository()->delete($this);
	}
	
	public function pk(){
		return $this->get($this->getRepository()->getPrimaryKeys()[0]);
	}
	
	
	
	public static function getRepository(){
		$repoclass = get_called_class() . 'Repository';
		if(class_exists($repoclass)){
			return new $repoclass;
		}else{
			return new Repository(get_called_class());
		}
	}


	/* --- Magic cliente -------------------- */
	public function __set($name,$value){ //A chorrada de get con _ deberia ser chamada por utra funcion distinta de get
		return $this->set($name,$value);
	}

	public function __get($name){
		return $this->get($name);
	}


	public function __isset($name){return $this->contains($name);}
	public function __unset($name){return $this->remove($name);}

	/*Array Access */
	public function offsetExists($offset){return $this->contains($offset);}
	public function offsetGet($offset){return $this->get($offset);}
	public function offsetSet($offset,$value){return $this->set($offset,$value);}
	public function offsetUnset($offset){return $this->remove($offset);}
	/**/

	public function __destruct(){

	}


	public function toArray(array $fields = null){

	    if($fields === null) {
            $oarr = (array)get_object_vars($this);
            unset($oarr['InitData']);
        }else{
            $oarr = array();
            foreach($fields as $f) $oarr[$f] = $this->$f;
        }
	    return $oarr;
    }
    public function toJson(array $fields = null){
        return new \fw\Json($this->toArray($fields));
    }



    public function getBinded($name, \fw\Form\Field $field)
    {
        return $this->$name;
    }

    public function setBinded($name, $value, \fw\Form\Field $field)
    {
        $this->$name = $value;
        return $value;
    }


    /**
     * @return \fw\Form\Field[]
     */
    public function getBindableFields(){
        $fields = [];
        $stru = $this->getRepository()->getStructure();
        foreach($stru as $name=>$val){
            if(isset($val['hidden'])) {
                continue;
            }

            $typeName = 'Text';
            if(isset($val['type'])){
                $typeName = $val['type'];
            }

            $typeClass = "\\fw\\Data\\Type\\".$typeName;
            $typeExist = class_exists($typeClass);
            $type = null;
            if($typeExist){
                $type = new $typeClass();
            }else{
                $type = new \fw\Data\Type\Text();
            }

            if(isset($val['required'])) {
                $type->required();
            }

            $fields[] = $type->getFormFieldInstance($name);
        }
        return $fields;
    }
}
?>