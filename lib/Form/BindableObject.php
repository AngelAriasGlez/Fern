<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 09/01/2018
 * Time: 17:58
 */

namespace fw\Form;


interface BindableObject
{
    public function getBinded($name , Field $field);
    public function setBinded($name, $value, Field $field);
}