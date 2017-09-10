<?php
namespace fw;

class Pdf
{
    private $Data;

    public function __construct($data)
    {
        $this->Data = $data;
    }


    public function __toString(){
        return $this->Data;
    }

}
