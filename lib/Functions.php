<?php
namespace fw;


function url_friendly($url){
    // Tranformamos todo a minusculas
    $url = strtolower($url);
    //Rememplazamos caracteres especiales latinos
    $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
    $repl = array('a', 'e', 'i', 'o', 'u', 'n');
    $url = str_replace ($find, $repl, $url);
    // Añadimos los guiones
    $find = array(' ', '&', '\r\n', '\n', '+');
    $url = str_replace ($find, '-', $url);
    // Eliminamos y Reemplazamos otros carácteres especiales
    $find = array('/[^a-z0-9\-<>]/', '/[\-]+/', '/<[^>]*>/');
    $repl = array('', '-', '');
    $url = preg_replace ($find, $repl, $url);
    return $url;
}


function Globals(){
	return Globals::getInstance();
}

function InputFilter($filter, $default = ''){
	return new InputFilter($filter, $default);
}
function DataFilter(){
	return new DataValidator();
}



/**
 * Create InputFilters from array ['name'=>['filter', 'default']]
 *
 * Ex: InputFilterFromArray($_GET, [['postal-code', InputFilter::NUMBER, null], ['name', InputFilter::ALL, null]]
 * @param array $inputfilters Data to filter
 * @param array $inputfilters
 *
 * @return array
 */
function InputFilterFromArray(array $data, array $inputFilters){
	$vars = [];
	foreach ($inputFilters as $n=>$f){
		if(isset($f[0]) && isset($f[1]) && isset($f[2])) {
			$vars[$f[0]] = InputFilter($data, $f[0], $f[1], $f[2])->getData();
		}elseif(isset($f[0]) && isset($f[1])) {
			$vars[$f[0]] = InputFilter($data, $f[0], $f[1])->getData();
		}elseif(isset($f[0])) {
			$vars[$f[0]] = InputFilter($data, $f[0])->getData();
		}
	}
	return $vars;
}


function lUri(array $path){
	return LocalUri::create()->setPath($path);
}

/**
 * Escanea un directorio (con slash) buscando un archivo con extensión '.ext'
 *
 * @param unknown_type $dir
 * @param unknown_type $ext
 */
function scandirext($dir,$ext)
{
	$files = scandir($dir);
	$findfiles=array();
	foreach($files as $file)
	{
		if ($file=='.' || $file=='..') continue;
		$extstr='.'.$ext;
		if ($val= strpos($file,$extstr)) $findfiles[]= $dir.$file;
	}
	return $findfiles;
}

function l($href, $content){return new Link($href, $content);}
function i($href, $alt=''){return new Image($href, $alt);}

function Link($href, $content){return new Link($href, $content);}
function Uri(){return new Uri();}
function Img($href, $alt=''){return new Image($href, $alt);}

function GetAction($method){return str_replace('_action', '', $method);}

/**
 * Enter description here...
 *
 * @param array $array
 * @param array $array2
 * @return unknown
 */
function array_mix(array $array, array $array2){
	$mix = array();
	foreach($array as $value){$mix[] = $value;}
	foreach($array2 as $value){$mix[] = $value;}
	return $mix;
}
/**
 * Busca en un array si tiene una parte de otro array
 *
 * @param array $array_needle
 * @param array $haystack
 * @return unknown
 */
function array2_search(array $array_needle, array $haystack){
	foreach($haystack as $val){
		if (array_search($val,$array_needle) !== false) return true;
		//	echo 'buscando '.$val.' con '.$array_needle.'<br />';
	}
	return false;
}
/**
 * Busca si está set la path solo soporta 2 niveles y ligera expresion regual account/*
 * @todo mas de dos niveles de path
 * @array $where donde buscar
 * @array $what que buscar
 * @return bool
 */
function isSetPath(array $where,array $what){
	$n=count($what);
	if ($n == 0) return false;
	$fulldir = $what[0].'/*';
	if (isset($where[$fulldir]) || ( ( $n >= 2 ) && isset($where[implode(DIRECTORY_SEPARATOR,$what)]))){
		return true;
	}
	return false;
}

/**
 * Busca una cadena dentro de un array
 *
 * @param unknown_type $string
 * @param unknown_type $array
 * @return utring key
 */
function array_psearch($string, $array){
	foreach ($array as $k=>$v){
		//echo 'Buscando ['.$string.'] en ['.$v.']<br />';
		if (strpos($v,$string) !== false) return $k;
	}
	return false;
}
/**
 * Vardump modificado
 *
 * @param mixed $var
 * @param bool $exit
 */
function vd($var, $exit = true){
	if (DEBUG) {

		if($exit){
			HttpHeaders::setContentType('html');
			HttpHeaders::exec();
			echo '<pre style="position:fixed;top:0;left:0;background:#fff;padding:40px;width:100%">';
			var_dump($var);
			echo '</pre>';
			flush();
			exit(0);
		}else{
			var_dump($var);
		}
	} else {
		throw new Exception("Vardump en modo no debug");
	}
}


function st($exit = true){
	if (DEBUG) {
		echo '<pre>';


		//ob_start();
		//ob_implicit_flush(0);
		debug_print_backtrace();
		//echo htmlentities(ob_get_clean());
		echo '</pre>';
		if($exit){
			HttpHeaders::setContentType('html');
			HttpHeaders::exec();
			exit(0);
		}
	} else {
		throw new Exception("Stacktrace en modo no debug");
	}
}
/**
 * Enter description here...
 *
 * @param unknown_type $name
 * @return unknown
 */
function isDataClass($name){
	$parent=get_parent_class($name);
	if(class_exists($name,false) &&($parent == 'DataRecord' || $parent == 'DataRepository')){
		return true;
	}
	return false;
}
/**
 * Enter description here...
 *
 * @param unknown_type $obj
 * @return unknown
 */
function isDataObj($obj){
	if($obj instanceof DataRecord || $obj instanceof DataRepository || $obj instanceof \fw\Data\Record || $obj instanceof \fw\Data\Repository){
		return true;
	}
	return false;
}
/**
 * Format to spanish number
 *
 * @param int $number
 * @return string
 */
function snf($number){
	return (string)number_format($number, 2, ',', '.');
}
/**
 * Cut string in next space
 *
 * @param string $string
 * @param int $len
 * @return string
 */
function cutim($string, $len){
	if(mb_strlen($string) > $len+1){
		$pos = mb_strpos($string, " ", $len+1);
		return mb_substr($string, 0, $pos);
	}else{
		return $string;
	}
}

/**
 * Enter description here...
 *
 * @param unknown_type $class
 * @param unknown_type $const
 * @return unknown
 */
function get_class_const($class, $const){
	$str = sprintf('%s::%s', $class, $const);
	if(defined($str)){
		return constant($str);
	}else{
		return false;
	}
}
///**
// *
// */
//if ( !function_exists('json_decode') ){
//	function json_decode($content){
//		$json = new json();
//		return $json->unserialize($content);
//	}
//}
///**
// *
// */
//if ( !function_exists('json_encode') ){
//	function json_encode($content){
//		$json = new json();
//		return $json->serialize($content);
//	}
//}

if (!function_exists('json_encode')){ // A eliminar en cuanto soporte PHP en modo nativo
	require 'JSON/JSON.php';
	function json_encode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->encode($arg);
	}

	function json_decode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->decode($arg);
	}

}


//if ( !function_exists('json_encode') ){
//	require 'jsonwrapper_inner.php';
//	require 'JSON/JSON.php';
//	function json_encode($arg)
//	{
//		global $services_json;
//		if (!isset($services_json)) {
//			$services_json = new Services_JSON();
//		}
//		return $services_json->encode($arg);
//	}
//}
//
//if ( !function_exists('json_decode') ){
//	function json_decode($arg)
//	{
//		global $services_json;
//		if (!isset($services_json)) {
//			$services_json = new Services_JSON();
//		}
//		return $services_json->decode($arg);
//	}
//}


/**
 * reemplaza la sintaxis de tipo según sql o mysql
 *
 * @param unknown_type $str
 * @param unknown_type $type
 */
/*
 function sqlsyntax($str,$type)
 {
 switch($type)
 {
 case 'mysql':
 return str_replace('"','`',$str);
 break;
 case 'pgsql':
 return str_replace('`','"',$str);
 break;
 default:
 break;
 }
 }
 */
/**
 * Transforma una forma IPV4 notacion por puntos a un decimal
 *
 * @param unknown_type $ip
 * @return unknown
 */
function inet_aton_IPV4($ip)
{
	$chunks = explode('.', $ip);
	return $chunks[0]*pow(256,3) + $chunks[1]*pow(256,2) + $chunks[2]*256 + $chunks[3];
}
/**
 * Calcula la inclusion en el CIDR IPV4 solamente
 * @param $CIDR
 * @param $IP
 * @return bool
 */
function netMatch ($CIDR,$IP) {
	list ($net, $mask) = explode ('/', $CIDR);
	return ( ip2long ($IP) & ~((1 << (32 - $mask)) - 1) ) == ip2long ($net);
}
/**
 * Split una String en un array
 * @todo eliminar cuando PHP soporte UTF8
 * @param $string
 * @return unknown_type
 */
function mbStringToArray ($string) {
	$strlen = mb_strlen($string);
	while ($strlen) {
		$array[] = mb_substr($string,0,1,"UTF-8");
		$string = mb_substr($string,1,$strlen,"UTF-8");
		$strlen = mb_strlen($string);
	}
	return $array;
}
/**
 * Devuelve el contenido completo de un archivo ZIP
 * @param $file
 * @return unknown_type
 */
function zip_get_contents($file){
	if (!file_exists($file)) return false;
	$zip = zip_open($file);
	if (is_resource($zip)) {
		while ($zip_entry = zip_read($zip)) {
			/*
			 echo "Name:               " . zip_entry_name($zip_entry) . "\n";
			 echo "Actual Filesize:    " . zip_entry_filesize($zip_entry) . "\n";
			 echo "Compressed Size:    " . zip_entry_compressedsize($zip_entry) . "\n";
			 echo "Compression Method: " . zip_entry_compressionmethod($zip_entry) . "\n";
			 */
			if (zip_entry_open($zip, $zip_entry, "r")) {
				$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
				zip_entry_close($zip_entry);
			}
		}
		zip_close($zip);
	}else{
		return false;
	}
	return $buf;
}
/**
 * Transforma un archivo en formato de retornos DOS a UNIX
 * @param $dosformat
 * @return unknown_type
 */
function dos2unix($dosformat) {
	return strtr($dosformat, array("\r" => ""));
}
/**
 * Transforma si lo detecta a codificacion UTF8
 * @param $file
 * @return unknown_type
 */
function file2UTF8($file) {
	$str=file_get_contents($file);
	$list=array('UTF-8','Windows-1251','Windows-1251','ISO-8859-15','ISO-8859-1','ASCII');
	$enc = mb_detect_encoding($str,$list);
	if ($enc != 'UTF-8'){
		$str=mb_convert_encoding($str,'UTF-8',$enc);
		//echo "Advertencia: Transformando codificación $enc a UTF-8";
		if (file_put_contents($file,$str) != true) HttpCode::send(500,null,false,"Can set encoding");
	}
}
?>
