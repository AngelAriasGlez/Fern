<?php
namespace fw\Data;


use fw\Annotations;
use fw\Data\Query;
use fw\Data\RepoQuery;

class Repository{
    private $Name;
    private $Structure = NULL;

    private static $STRUCTURE_CACHE = [];

    private $Db;


    public function __construct($tablename = NULL, DBContext $db = NULL){





        $class = get_class($this);
        if($tablename !== NULL){
            $this->Name = $tablename;
        }elseif(strpos($class, 'Repository') !== false){
            $this->Name = str_replace('Repository', '', $class);
        }else{
            throw new \Exception('Couldn\'t find table name');
        }

        if($db){
            $this->Db = $db;
        }else{
            $this->Db = \fw\Config::getDefaultDB();
            if(!$this->Db) {
                throw new \Exception("Database context no set for '$this->Name'");
            }

        }
        $virtual = false;
        if(!class_exists($this->Name)){
            eval("class $this->Name extends \\fw\\DataRecord{};");
            $virtual = true;
        }


        if(isset(self::$STRUCTURE_CACHE[$this->Name])){
            $this->Structure = self::$STRUCTURE_CACHE[$this->Name];
        }else{
            $this->Structure = Annotations::getPropertiesAnnotations($this->Name);
            $rclass = new \ReflectionClass($this->Name);
            $primarykeyName = 'Id';

            if(count(array_column($this->Structure, 'primary')) == 0){
                $this->Structure[$primarykeyName] = array('datatype'=>'INT(11) UNSIGNED', 'primary'=>true, 'autoincrement'=>true, 'notnull'=>true, 'hidden'=>true);
            }

            foreach($this->Structure as $colname=>$attrs){

                if(array_key_exists('skip', $attrs) || ($rclass->hasProperty($colname) && $rclass->getProperty($colname)->isStatic())){
                    unset($this->Structure[$colname]);
                    continue;
                }
                if(!array_key_exists('datatype', $attrs)){
                    if(array_key_exists('primary', $attrs)){
                        $this->Structure[$colname]['datatype'] = 'INT';
                    }else {
                        $this->Structure[$colname]['datatype'] = 'VARCHAR(3000)';
                    }
                }
                if(array_key_exists('foreing', $attrs)){

                    $fk = explode('.', $attrs['foreing']);
                    $className = $fk[0];
                    $obj = new $className();
                    $frepo = $obj->getRepository();

                    if(isset($fk[1])){
                        $f = $fk[1];
                        $t = $frepo->getFieldDatatype($fk[1]);
                    }else{
                        $pkey = $frepo->getPrimaryKey();
                        $f = $pkey;
                        $t = $frepo->getPrimaryKeyDatatype($pkey);
                    }

                    $this->Structure[$colname]['datatype'] = $t;
                    $this->Structure[$colname]['foreing'] = [$className, $f];

                }
            }
            $sql = [];
            $pks = [];
            $kys = [];
            foreach($this->Structure as $colname=>$attrs){
                $cl = "`$colname`";
                if(array_key_exists('datatype', $attrs)){
                    $cl .= ' '.$attrs['datatype'];
                }
                if(array_key_exists('notnull', $attrs)){
                    $cl .= ' NOT NULL';
                }
                if(array_key_exists('autoincrement', $attrs)){
                    $cl .= ' AUTO_INCREMENT';
                }
                if(array_key_exists('unique', $attrs)){
                    $cl .= ' UNIQUE';
                }
                if(array_key_exists('primary', $attrs)){
                    $pks[] = $colname;
                }
                if(array_key_exists('key', $attrs)){
                    $kys[] = $colname;
                }
                if(array_key_exists('foreing', $attrs)){
                    $sql[] = "FOREIGN KEY (`$colname`) REFERENCES ".$attrs['foreing'][0]."(`".$attrs['foreing'][1]."`)";
                }

                $sql[] = $cl;
            }

            if(count($pks) > 0) $sql[] = "PRIMARY KEY (`".implode('`,`' , $pks)."`)";
            foreach($kys as $k){
                $sql[] = "KEY (`$k`)";
            }

            $sql = "CREATE TABLE IF NOT EXISTS `".$this->Name."` (".implode($sql, ",\n").")";

            try{
                $st = $this->Db->exec($sql);
            }catch(\Exception $e){
                throw new \Exception("Error to create table `$this->Name`, ".$e->getMessage()."\n\n$sql \n\n");
            }


            self::$STRUCTURE_CACHE[$this->Name] = $this->Structure;
        }
    }
    public function getDbContext(){
        return $this->Db;
    }

    public static function getInstance(){
        return new static();
    }
    public function getName(){
        return $this->Name;
    }
    public function getColumns(){
        return array_keys($this->Structure);
    }
    public function getStructure(){
        return $this->Structure;
    }
    public function getPrimaryKeys(){
        $out = array();
        foreach($this->Structure as $name=>$cols){
            if(array_key_exists('primary', $cols)){
                $out[] = $name;
            }
        }
        return $out;
    }
    public function getPrimaryKey($index = 0){
        $keys = $this->getPrimaryKeys();
        if(isset($keys[$index])) return $keys[$index];
        return null;
    }
    public function getFieldDatatype($fname){
        if(isset($this->Structure[$fname]['datatype'])){
            return $this->Structure[$fname]['datatype'];
        }
        return NULL;
    }
    public function getPrimaryKeyDatatype($name){
        return $this->getFieldDatatype($name);
    }


    public function findBy($name , $values ){
        if (is_null($name) || is_null($values)){
            return  NULL;
        }
        $where = Query\Where::build();

        if(is_array($values)){
            foreach($values as $value){
                if ($value instanceof DataRecord){$value = $values->pk();}
                $arr[] = addslashes($value);
            }
            $where->in($this->getName().'.'.$name, $arr);
        }else{
            if ($values instanceof DataRecord){$values = $values->pk();}
            $where->equals($this->getName().'.'.$name, $values);
        }

        return $this->query()->where($where)->exec();
    }



    public function findWhere(Query\Where $where){
        return $this->query()->where($where)->exec();
    }

    /**
     * @return RecordCollection
     */
    public function findAll(){
        return $this->query()->exec();
    }


    public function findBySql($sql){
        return $this->fetch($this->sqlQuery($sql));
    }

    public function findByPk($value){
        $query = Query\Where::build();
        $keys = $this->getPrimaryKeys();

        if(is_array($value)) {
            $last_key = end(@array_keys($keys));
            foreach ($keys as $k => $v) {
                $query->equals($v, $value[$v]);
                if ($k != $last_key) $query->and();
            }
        }else if(isset($keys[0])){
            $query->equals($keys[0], $value);
        }else{
            return null;
        }

        return $this->query()->where($query)->limit(1)->exec()->first();
    }

    /**
     * @return RepositorySelect
     */
    public function query(){
        $query = new RepositorySelect($this);
        $query->from($this->getName());
        return $query;
    }

    public function sqlQuery($query){
        if($query instanceof Query) return $query->exec();

        return Query::build()->exec($query);
    }





    public function save($records){

        $resultKeys = array();
        if($records instanceof \fw\Data\Record) $records = array($records);

        foreach($records as $record){
            if(count($record->getModified()) === 0){
                return false; // Nada que salvar

            }

            $fields = array();
            $values = array();

            foreach($this->Structure as $name=>$more){
                $value = $record->$name;
                //Recursive save
                if(\fw\isDataObj($value)){
                    $value->save();
                    $value = $value->pk();
                }
                if(!(array_search($name, $this->getPrimaryKeys()) !== false && $value === NULL)){
                    $fields[$name] = "`{$name}` = ?";
                    if(is_array($value) || $value instanceof Json) {
                        $values[] = json_encode($value);
                        /*var_dump($name);
                        var_dump(substr(strip_tags(json_encode($value)), 0, 100));
                        switch(json_last_error()) {
                            case JSON_ERROR_NONE:
                                echo ' - Sin errores';
                                break;
                            case JSON_ERROR_DEPTH:
                                echo ' - \Excedido tamaño máximo de la pila';
                                break;
                            case JSON_ERROR_STATE_MISMATCH:
                                echo ' - Desbordamiento de buffer o los modos no coinciden';
                                break;
                            case JSON_ERROR_CTRL_CHAR:
                                echo ' - Encontrado carácter de control no esperado';
                                break;
                            case JSON_ERROR_SYNTAX:
                                echo ' - Error de sintaxis, JSON mal formado';
                                break;
                            case JSON_ERROR_UTF8:
                                echo ' - Caracteres UTF-8 malformados, posiblemente están mal codificados';
                                break;
                            default:
                                echo ' - Error desconocido';
                                break;
                        }*/
                    }else{
                        $values[] = $value;
                    }
                }

            }



            $implodedFields=implode(',',$fields);

            $query ="INSERT INTO `{$this->Name}` SET {$implodedFields} ON DUPLICATE KEY UPDATE {$implodedFields}";
            //echo $query;


            $sta = $this->Db->prepare($query);

            try{
                $sta->execute(array_merge($values, $values));
            }catch(\Exception $e){
                throw new \Exception("SQL Query execution error on table $this->Name \n\n ".$e->getMessage()."\n\n $query \n\n ");
            }

            if(!$sta) {
                $err = $this->Db->errorInfo();
                throw new \Exception('SQL Query execution error: <strong style="color:red">'.$err[2].'</strong>');
            }

            $key = $this->Db->lastInsertId($this->Name); // Non funciona con statements intermedios
            if($key) {
                $record->set($this->getPrimaryKey(), $key);
                $resultKeys[] = $key;
            }
        }

        return $resultKeys;
    }

    public function delete($records){
        if($records instanceof \fw\Data\Record) $records = array($records);

        foreach($records as $record){
            $keys = array();
            foreach($this->getPrimaryKeys() as $k){
                $keys[] = "{$k}='".addslashes($record->get($k))."'";
            }


            $query = "DELETE FROM `{$this->Name}` WHERE ".implode(' AND ', $keys);

            try{
                return $this->Db->exec($query);
            }catch(\Exception $e){
                throw new \Exception("SQL Query execution error on table $this->Name \n\n ".$e->getMessage()."\n\n $query \n\n ");
            }

        }
        return 0;
    }


    public function isColumn($name){
        if (isset(array_change_key_case($this->Structure)[strtolower($name)])) return true;
        return false;
    }






    public function count(){
        $query = $this->sqlQuery("SELECT COUNT(*) FROM `{$this->Name}`");
        return $query[0][0];
    }


    /**
     * @param $instance
     * @return RecordCollection
     */
    public function fetch($res){
        /*if(!$res){
            return null;
        }*/
        $collectionClass = $this->Name.'Collection';
        if(class_exists($collectionClass)){
            $ret = new $collectionClass();
        }else{
            $ret = new \fw\Data\RecordCollection();
        }

        foreach($res as $row){
            /*foreach($row as $k=>$f){
                if(isset($this->Structure[$k]['type'])){
                    $classType = '\\fw\\Data\\Type\\'.$this->Structure[$k]['type'];
                    if(class_exists($classType)){
                        $row[$k] = new $classType($f);
                    }
                }
            }*/
            $obj = new $this->Name();
            $obj->hydrate($row);
            $ret->add($obj);
        }
        return $ret;
    }

}
?>