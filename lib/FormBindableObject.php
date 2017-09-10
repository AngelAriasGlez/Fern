<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 09/01/2018
 * Time: 17:58
 */

namespace fw;


interface FormBindableObject
{
    public function getBindedField($name);
    public function setBindedField($name, $value);
}