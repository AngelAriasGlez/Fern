<?php
namespace fw\Data;
use fw\Data\Repository;

class RepositorySelect extends Query\Select {
    /**
     * @var \fw\Data\Repository
     */
    public $Repository;

    /**
     * RepoQuery constructor.
     * @param $repo \fw\Data\Repository
     */
    public function __construct($repo){
        $this->Repository = $repo;

        $this->from($repo->getName());

        $columns = $repo->getColumns();
        foreach ($columns as $v){
            $this->addColumn($v);
        }
        //$this->addColumn($repo->getName().'.*');

        /*$structure = $repo->getStructure();
        foreach($structure as $k=>$attrs){
            if(array_key_exists('foreing', $attrs)){
                $f = $attrs['foreing'];
                $obj = new $f[0]();
                $objStruct = $obj->getRepository()->getStructure();
                $default = $this->searchForDefault($objStruct);
                if($default){
                    $this->join($k, $f[0], $f[0].'.'.$f[1]);

                    $this->Columns[] = "$f[0].$default $k";
                }
            }
        }*/


    }
    public function getRepository(){
        return $this->Repository;
    }

    /**
     * @return RecordCollection
     */
    public function exec($query = NULL){
        return $this->Repository->fetch(parent::exec());
    }

    function searchForDefault($array) {
        foreach ($array as $key => $val) {
            if(array_key_exists ('default', $val)) return $key;
        }
        return null;
    }




}

