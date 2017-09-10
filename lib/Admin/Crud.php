<?php
namespace fw\Admin;

class Crud extends  \fw\Controller\Crud{

    public function __construct($objectName)
    {
        parent::__construct($objectName);
        $this->setBaseTemplate(dirname(__FILE__).'/Admin.tpl');
    }
}