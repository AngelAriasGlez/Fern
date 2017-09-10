<?php
namespace fw;
require_once('third-party/tcpdf/tcpdf.php');

class Pdf
{
    private $Pages;

    public function addPage($html){
        $this->Pages[] = $html;
    }



    public function render(){

        // create new PDF document
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        foreach($this->Pages as $p){

            $pdf->AddPage();
            $pdf->writeHTML($p, true, 0, true, true);
        }

        return $pdf->Output(null, 'S');
    }
    public function __toString(){
        return $this->render();
    }

}
