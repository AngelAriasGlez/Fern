<?php

namespace fw;
use Respect\Validation\Validator as v;

class FormFieldPhoto extends FormField{
    private $ResampleSize;
    private $UploadName;
    function __construct($name, $resamplesize = 1920)
    {
        $this->UploadName = '__'.$name;
        $this->ResampleSize = $resamplesize;
        
        $this->Validator = new v();
        parent::__construct($name);

        $this->addHtmlField(new HtmlFormField($this->UploadName, HtmlFormField::FILE));
    }

    public function validate()
    {
        return parent::validate();
    }


    public function getRawData(){
        $name = $this->getName();
        if(isset($_FILES[$this->UploadName]) && !empty($_FILES[$this->UploadName]["tmp_name"])){
            $file = $_FILES[$this->UploadName]["tmp_name"];
            if(!empty($file) && file_exists($file)) {
                $imagestring = file_get_contents($file);
                //var_dump(substr($imagestring, 0, 200));

                $porcentaje = 0.5;
                $image = imagecreatefromstring($imagestring);
                $width_orig = imagesx($image);
                $height_orig = imagesy($image);

                $width = $this->ResampleSize;
                $height = $this->ResampleSize;

                $ratio_orig = $width_orig/$height_orig;

                if ($width/$height > $ratio_orig) {
                    $width = $height*$ratio_orig;
                } else {
                    $height = $width/$ratio_orig;
                }

                $image_p = imagecreatetruecolor($width, $height);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

                ob_start();
                imagejpeg($image_p, null, 85);
                imagedestroy( $image_p );
                $out = ob_get_clean();
                return $out;

            }




        }else if(isset($_REQUEST[$name])){
            //if($_REQUEST[$name] == 'NULL') return null;
            return $_REQUEST[$name];
        }else{

        }
        return null;

    }
}