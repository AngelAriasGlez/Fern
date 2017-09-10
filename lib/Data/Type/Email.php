<?php
/**
 * Created by PhpStorm.
 * User: angelariasgonzalez
 * Date: 11/2/18
 * Time: 22:00
 */
namespace fw\Data\Type;
use fw\Data\Type;
use Respect\Validation\Validator as v;
class Email extends Type
{
    public function __construct($data = null)
    {
        parent::__construct($data);
        $this->Validator = v::optional(v::email());
    }


}