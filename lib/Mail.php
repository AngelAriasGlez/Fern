<?php
namespace fw;

class Mail
{
    private $Subject;
    private $Body;
    private $To;
    private $FromEmail;
    private $FromName;

    public function __construct($to=null, $body=null, $subject=null){
        $this->To = $to;
        $this->Subject = $subject;
        $this->Body = $body;
    }

    public function setSubject($subject){
        $this->Subject = $subject;
    }
    public function setBody($body){
        $this->Body = $body;
    }
    public function setTo($email, $name=null){
        if($name){
            $this->To = "\"$name\" <$email>";
        }else{
            $this->To = "$email";
        }
    }
    public function setFrom($email, $name=null){
        if($name){
            $this->From = "\"$name\" <$email>";
        }else{
            $this->From = "$email";
        }
    }

    public function send(){

        $nheaders = '';
        if($this->From) $nheaders .= "From: " . $this->From . "\r\n";
        $nheaders .= "MIME-Version: 1.0\r\n";
        $nheaders .= "Content-Type: text/html;charset=UTF-8\r\n";

        mail($this->To,'=?utf-8?B?'.base64_encode($this->Subject).'?=', $this->Body, $nheaders);
    }

    public static function resendFormData(Form $form, $to, $subject, $emailFielName, $nameFielName, array $extra = array()){
        $mail = new \fw\Mail();
        $mail->setTo($to);
        $body = '';
        foreach($form->getAllFields() as $f){
            $body .= '<strong>'.$f->getLabel().':</strong> '.$form->getValue($f->getName()).'<br>';
        }
        foreach($extra as $k=>$v){
            $body .= '<strong>'.$k.':</strong> '.$v.'<br>';
        }
        $mail->setBody($body);
        $mail->setFrom($form->getValue($emailFielName), $form->getValue($nameFielName));
        $mail->setSubject($subject);
        $mail->send();
    }

}
