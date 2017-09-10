<?php
namespace fw;

class Helper {
    private $Template;

    public function getTemplate(){
        return $this->Template;
    }
    public function setTempalte(Template $template){
        $this->Template = $template;
        return $this;
    }

    public function __toString()
    {
        if($this->Template == null)return '';
        $this->Template->self = $this;
        return $this->Template->__toString();
    }

}
?>