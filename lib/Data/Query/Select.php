<?php
namespace fw\Data\Query;
use fw\Data\Query\Where;

class Select extends \fw\Data\Query{
    protected $Columns = array();
    protected $Source;
    protected $SourceAlias;
    protected $Condition;
    protected $HavingCondition;
    protected $LimitCount = 0;
    protected $LimitStart = 0;
    protected $Join = array();
    protected $Order = array();
    protected $Group = array();


    public function __construct()
    {

    }
    public function count(){
        $prevCols = $this->Columns;
        array_shift($this->Columns);
        $this->Columns = [['COUNT(*)','count']];
        $sta = $this->_exec();

        $this->Columns = $prevCols;
        return array_values($sta[0])[0];
    }
    public function select(array $array){
        $this->Columns = $array;
        return $this;
    }
    public function addColumn($name, $alias = null){
        $this->Columns[] = [$name, $alias];
        return $this;
    }
    public function getColumns(){
        return $this->Columns;
    }

    public function setColumns($cols){
        $this->Columns = $cols;
        return $this;

    }
    public function clearColumns(){
        $this->Columns = [];
        return $this;
    }
    public function from($source, $alias = null){
        $this->Source = $source;
        $this->SourceAlias = $alias;
        return $this;
    }
    public function where($where){
        $this->Condition = $where;
        return $this;
    }
    public function having($where){
        $this->HavingCondition = $where;
        return $this;
    }

    /**
     * @return Where
     */
    public function getWhere(){
        if(!$this->Condition) $this->Condition = new Where();
        return $this->Condition;
    }
    public function limit($count, $start = 0){
        $this->LimitCount = $count;
        $this->LimitStart = $start;
        return $this;
    }
    public function limitStart($start){
        $this->LimitStart = $start;
        return $this;
    }
    public function limitCount($count){
        $this->LimitCount = $count;
        return $this;
    }
    public function join($thisField, $to, $toField){
        $this->Join[] = [$to, $thisField, $toField];
    }

    public function order($name, $order = 'DESC'){
        $this->Order[$name] = $order;
    }
    public function group($name, $order = 'DESC'){
        $this->Group[$name] = $order;
    }


    public function __toString(){


        $sql = "SELECT ";

        if(count($this->Columns) <= 0){
            $sql .= '*';
        } else{

            $impFun = function($v){
                //if(!isset($v) && !is_array($v)) throw new \Exception('Select column must be array');
                $v = array_values($v);
                if(!isset($v[1])) return $v[0];

                return $v[0].' `'.$v[1].'`';
            };
            $sql .= implode(', ', array_map($impFun, $this->Columns));
        }

        $sql .= " FROM {$this->Source}";
        if(isset($this->SourceAlias)) $sql .= " `{$this->SourceAlias}`";

        if(count($this->Join) > 0){
            foreach($this->Join as $j) {
                $sql .= " LEFT JOIN $j[0] ON $j[1]=$j[2]";
            }
        }

        if(isset($this->Condition) && $cond = $this->Condition->__toString()){
            $sql .= ' WHERE '.$cond;
        }

        if(count($this->Group)){
            $sql .= " GROUP BY ".implode(', ', array_map(function ($v, $k) { return $k.' '.$v; }, $this->Group, array_keys($this->Group)));

        }

        if(isset($this->HavingCondition) && $cond = $this->HavingCondition->__toString()){
            $sql .= ' HAVING '.$cond;
        }

        if(count($this->Order)){
            $sql .= " ORDER BY ".implode(', ', array_map(function ($v, $k) { return $k.' '.$v; }, $this->Order, array_keys($this->Order)));

        }

        if($this->LimitCount || $this->LimitStart){
            $sql .= " LIMIT {$this->LimitStart},{$this->LimitCount}";
        }

        return $sql;
    }
    public function getBindedValues(){
        $out  = [];
        if($this->Condition) $out = $this->Condition->getBindedValues();
        if($this->HavingCondition) $out = array_merge($out, $this->HavingCondition->getBindedValues());
        return $out;
    }
}
function Select(){
    return Select::build();
}
