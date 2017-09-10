<?php
namespace fw\Data\Query;
class Union extends \fw\Data\Query{
    private $Querys = array();
    public function __construct(){

    }

    public function add($query){
        $this->Querys[] = $query;
        return $this;
    }

    public function __toString(){
        return '(('.implode(') UNION (', $this->Querys).'))';
    }

};
function Union(){
    return Union::build($args);
}
?>