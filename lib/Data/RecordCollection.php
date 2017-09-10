<?php
/**
 * Created by PhpStorm.
 * User: Angel
 * Date: 26/04/2017
 * Time: 23:32
 */

namespace fw\Data;


class RecordCollection implements \ArrayAccess,\Countable,\Iterator
{
	/**
	 * @var DataRecord[]
	 */
	protected $Data = array();

	/**
	 * @param DataRecord $record
	 */
	public function add(Record $record){$this->Data[] = $record;}


	public function count(){
		return count($this->Data);
	}

	public function rewind(){
		reset($this->Data);
	}

	public function current(){
		return current($this->Data);
	}

	public function key(){
		return key($this->Data);
	}

	public function next(){
		return next($this->Data);
	}

	public function valid(){
		$key = key($this->Data);
		return ($key !== NULL && $key !== FALSE);
	}
	public function first(){
		if(isset($this->Data[0])) {
			return $this->Data[0];
		}else{
			return null;
		}
	}

	public function offsetExists($offset){return isset($this->Data[$offset]);}
	public function offsetGet($offset){return $this->Data[$offset];}
	public function offsetSet($offset,$value){$this->Data[$offset] = $value;}
	public function offsetUnset($offset){unset($this->Data[$offset]);}

    public function toIdArray($fieldName){
	    $out = array();
	    foreach($this->Data as $r){
	        $out[$r->pk()] = $r->$fieldName;
        }
        return $out;
    }
    public function toPkArray($fieldName){
        $out = array();
        foreach($this->Data as $r){
            $out[$r->pk()] = $r->$fieldName;
        }
        return $out;
    }
    public function toAutoArray($fieldName){
        $out = array();
        foreach($this->Data as $r){
            $out[] = $r->$fieldName;
        }
        return $out;
    }
    public function toNameValueArray($keyFieldName, $valueFieldName){
        $out = array();
        foreach($this->Data as $r){
            $out[$r->$keyFieldName] = $r->$valueFieldName;
        }
        return $out;
    }
	public function toArray(array $fields = null){
	    if($fields === null) return $this->Data;
        $out = array();
        foreach($this->Data as $k=>$f){
            $out[] = $this->Data[$k]->toArray($fields);
        }
        return $out;
    }
    public function toJson(array $fields = null){
        return new \fw\Json($this->toArray($fields));
    }



    public function delete(){
        foreach($this->Data as $k=>$f){
            $f->delete();
        }
    }
    public function save(){
        foreach($this->Data as $k=>$f){
            $f->save();
        }
    }
}