<?php
namespace fw;

class FormHandler
{
    private $Form;

    private $Submitted = false;
    private $Errors = array();
    private $Values = array();

    private $BindedObjs = array();


    public function __construct(Form &$form)
    {
        $this->Form = &$form;

        if (isset($_SESSION['fw\Form'][$this->Form->getId()]) && isset($_REQUEST[$this->Form->getId() . '_hash'])) {
            if ($_REQUEST[$this->Form->getId() . '_hash'] == $_SESSION['fw\Form'][$this->Form->getId()]) {
                $this->Submitted = true;
            }
        }

    }





























    /**
     * Devuelve un error para Ajax
     *
     * @param array $value
     */
    public static function ajaxErrors(array $value)
    {
        foreach ($value as $key => $msg) {
            $value[$key] = "'$key':'$msg'";
        }
        BaseTemplate::disable();
        fwBaseController::flush("{'err': {" . implode(',', $value) . "}}", 'json');
    }

    /**
     * Redirige con Ajax
     *
     * @param unknown_type $href
     */
    public static function ajaxRedirect($href)
    {
        if ($href instanceof Href) {
            $href = $href->__toString();
        }
        BaseTemplate::disable();
        fwBaseController::flush("{'location': '$href'}", 'json');
    }

    /**
     * Envia un mensaje con Ajax
     *
     * @param unknown_type $value
     * @param unknown_type $reset
     */
    public static function ajaxMessage($value, $reset = false)
    {
        BaseTemplate::disable();
        fwBaseController::flush("{'msg': '$value', reset: " . (($reset) ? 'true' : 'false') . "}", 'json');
    }


}