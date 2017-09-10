<?php
namespace fw\Admin;

class Base extends  \fw\Controller\Crud{

    public function __construct()
    {
        $this->setBaseTemplate(dirname(__FILE__).'/Admin.tpl');
    }
}