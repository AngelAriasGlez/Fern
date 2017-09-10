<?php
/**
 * Template base por defecto
 *
 */
namespace fw;
class BaseTemplate {
	private $Template = NULL;
	private $Vars = array();
	private $Disable = false;
	private $Title;
	
	private $Style = array();
	private $Script = array();
	private $ScriptLibs = array();
	private $Content = '';
	
	//private $CallStyles = false;
	//private $CallScripts = false;

	public function __construct($templatePath ){
        $this->setTemplate($templatePath);
    }

    public function setTemplate($value){
		if (file_exists($value))
			$this->Template = $value;
		else
			throw new Exception("Base Template not found : ". $value);
	}

	public function setVar($var, $value){$this->Vars[$var] = $value;}

	public function setTitle($value){$this->Title = $value;}

	public function getContent(){return $this->Content;}

	public function getTitle(){return $this->Title;}
    public function getLanguage(){
	    return Language::getPreferedLang();
	}

	public function getStyles(){
        //$this->CallStyles = true;
        return '<style type="text/css">'.implode('', $this->Style).'</style>';
	}
	public function getScripts(){
        //$this->CallScripts = true;
		$libs = '';
		foreach($this->ScriptLibs as $src){
			$libs .= '<script src="'.$src.'"></script>';
		}

		return $libs.'<script type="text/javascript">'.implode('', $this->Script).'</script>';
	}
    public function setContent($content){
        $this->Content = $content;
    }

	public function render() {

		if(!$this->Disable && $this->Template != null){

			$checkTemplate = new Template($this->Template);
            $checkTemplate->setVar('self', $this);
			$checkTemplate->render();

			//if($this->CallStyles)
			    $this->Content = $this->extractStyles($this->Content);
			//if($this->CallScripts)
			    $this->Content = $this->extractScripts($this->Content);

			$baseTemplate = new Template($this->Template);
            $baseTemplate->setVar('self', $this);

			foreach ($this->Vars as $key=>$var){$baseTemplate->setVar($key, $var);}
			return  $baseTemplate->render(false);
		}else{
			return false;
		}
	}

	public function extractStyles($str){

			preg_match_all('@<style[^>]*?>(.*?)</style>@si', $str, $style);

			foreach($style[1] as $s){$this->Style[] = $s;}

			foreach($style[0] as $a){$str = str_replace($a, '', $str);}

			return $str;

	}
	public function extractScripts($str){
		preg_match_all('@<script( src="(.*?)")?[^>]*?>(.*?)</script>@si', $str, $script);

		foreach($script[3] as $s){
			if(empty(($s))) continue;
			$this->Script[] = $s;
		}
		foreach($script[2] as $s){
			if(empty(($s))) continue;
			$this->ScriptLibs[] = $s;
		}

		foreach($script[0] as $a){$str = str_replace($a, '', $str);}

		return $str;

	}

	/**
	 * Desactiva el template default
	 *
	 * @param unknown_type $out
	 */
	public function disable(){
		$this->Disable = true;
	}

	public function __toString()
    {
        try {
            return $this->render();
        }catch(\Exception $e){
            var_dump($e);

        }
    }
}
?>