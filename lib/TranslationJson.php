<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 08/01/2018
 * Time: 12:07
 */

namespace fw;

class TranslationJson extends \fw\Json
{



    public function __toString()
    {
        return $this->getCurrentLang();
    }
    public function getCurrentLang(){

        $langs = Language::getPreferedLangs();
        foreach($langs as $l){
            //$l = addslashes($l);
            if(isset($this->Data[$l]) && !empty($this->Data[$l])) return utf8_decode($this->Data[$l]);
        }

        if(isset($this->Data) && is_array($this->Data) && count($values = array_values($this->Data)) > 0) {
            return utf8_decode($values[0]);
        }

        return '';
    }

    public function getLang($lang, $defaultOnNull){
        if(isset($this->Data[$lang])) {
            return utf8_decode($this->Data[$lang]);
        }else{
            return self::getCurrentLang();
        }
    }

}