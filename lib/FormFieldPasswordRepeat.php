<?php

namespace Respect\Validation\Rules;

class PasswordEquals extends AbstractRule
{
    public $compareTo;

    public function __construct($compareTo)
    {
        $this->compareTo = $compareTo;
    }

    public function validate($input)
    {
        return $input == $this->compareTo;
    }
}
namespace Respect\Validation\Exceptions;

class PasswordEqualsException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'La contraseña no coincide',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => 'La contraseña no debe coincidir',
        ],
    ];
}
namespace fw;
use Respect\Validation\Validator as v;


class FormFieldPasswordRepeat extends FormField{
    function __construct($name, FormFieldPassword $field)
    {
        parent::__construct($name);

        $this->setLabel('Repeat password');
        $val = $field->getRawData();
        $this->Validator = v::PasswordEquals($val);
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::PASSWORD));
    }
}