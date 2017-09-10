<?php


namespace Respect\Validation\Rules\Locale;
use Respect\Validation\Rules\AbstractRule;

class EsIdentityCard extends AbstractRule
{
    public function validate($input)
    {
        if(strlen($input) != 9) return false;
        $letra = substr($input, -1);
        $numeros = substr($input, 0, -1);
        if ( substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1) == $letra && strlen($letra) == 1 && strlen ($numeros) == 8 ){
            return true;
        }else{
            return false;
        }
    }
}
namespace Respect\Validation\Exceptions\Locale;
use Respect\Validation\Exceptions\ValidationException;
class EsIdentityCardException extends ValidationException
{
    /**
     * @var array
     */
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} debe ser válido',
        ],
        self::MODE_NEGATIVE => [
            self::STANDARD => '{{name}} no debe ser válido',
        ],
    ];
}




namespace fw;
use Respect\Validation\Validator as v;

class FormFieldId extends FormField{
    function __construct($name, $countryCode)
    {
        parent::__construct($name);
        $this->Validator = v::identityCard($countryCode);
        $this->addHtmlField(new HtmlFormField($name, HtmlFormField::TEXT));
    }
}