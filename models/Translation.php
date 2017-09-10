<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 08/01/2018
 * Time: 12:07
 */
namespace fw\models;
class Translation extends fw\DataRecord
{
    public $Lang;       #DT varchar(15)
    public $Object;     #DT varchar(50)
    public $Field;      #DT varchar(50)
    public $ObjectId;   #DT varchar(50)
    public $Data;       #DT Text



    public function __toString()
    {
        return $this->Data;
    }
    public static function getCurrent($object, $fieldName, $id){
        $res =  self::getAll($object, $fieldName, $id);
        $langs = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        $langs = explode(',', $langs[0]);
        foreach($langs as $l){
            $l = addslashes($l);
            if(isset($res[$l])) return $res[$l];
        }
        if(isset($res[0])){
            return $res[0];
        }
        return null;
    }

    public static function getAll($object, $fieldName, $id){
        $className = get_class($object);
        return Translation::getRepository()->findWhere("`Object`='{$className}' AND `Field`='{$fieldName}' AND `ObjectId`='{$id}'")->toNameValueArray('Lang', 'Data');
        //return Translation::getRepository()->findWhere("`Object`='{$className}' AND `Field`='{$fieldName}' AND `ObjectId`='{$id}'");
    }
    public static function saveOne($object, $fieldName, $id, $lang, $text){
        $trans = new Translation();
        $trans->Object = get_class($object);
        $trans->Field = $fieldName;
        $trans->ObjectId = $id;
        $trans->Lang = $lang;
        $trans->Data = $text;
        $trans->save();

    }
    public static function saveAll($object, $fieldName, $id, $texts){
        foreach($texts as $lang=>$text){
            self::saveOne($object, $fieldName, $id, $lang, $text);
        }

    }
}