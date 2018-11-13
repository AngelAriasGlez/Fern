<?php
namespace fw\Data;
use fw\Data\Query\Where;

class Query{
    public static function build(){
        return new static();
    }

    /**
     * @param null $query
     * @return array
     * @throws \Exception
     */
    public function exec($query = null){
        return $this->_exec($query);
    }
    protected function _exec($query = null){
        $db = \fw\Config::getDefaultDB();

        if($query === null) {
            $query = $this->__toString();
        }
        //var_dump($query);
        try{
            $sta = $db->prepare($query);
            $sta->execute($this->getBindedValues());
        }catch(\PDOException $e){
            throw new \Exception("SQL Query execution error $query \n\n ".$e->getMessage()."\n\n $query \n\n ");
        }
        if(!($sta instanceof \PDOStatement)){
            $err = $this->_db->errorInfo();
            throw new Exception("SQL Query execution error on table $this->Source : \n\n <strong style='color:red'>$err[2]</strong>\n\n $query \n\n ");
        }
        return $sta->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getBindedValues(){
        return [];
    }
}
