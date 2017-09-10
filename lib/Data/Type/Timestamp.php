<?php
/**
 * Created by PhpStorm.
 * User: angelariasgonzalez
 * Date: 11/2/18
 * Time: 22:00
 */
namespace fw\Data\Type;
use fw\Data\Type;

class Timestamp extends Type
{


    public function __toString()
    {
        return ucfirst(strftime("%c", $this->Data));
    }

}