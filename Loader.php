<?php
/**
 * Estadisticas
 */

namespace fw {

    use fw\Http\Header;
    use fw\Http\Headers;
    use Respect\Validation\Rules\LanguageCode;

    if (!defined('DEBUG')) define('DEBUG', false);

    session_start();

	if (DEBUG) list($useg, $seg) = explode(' ', microtime());
	if (DEBUG) $GLOBALS['SCRIPT_INIT_TIME'] = $useg + $seg;
	if (DEBUG) $GLOBALS['DB_TIME'] = 0.000;
	if (DEBUG) ini_set('display_errors', '1'); else ini_set('display_errors', '0');

	define('DIR_SEP', DIRECTORY_SEPARATOR);
	define('URI_SEP', '/');

	function dcc($a, $b)
	{
		return rtrim(rtrim($a, '/\\') . URI_SEP . ltrim($b, '/\\'), '/\\');
	}

	/**
	 * Definitions y constantes
	 * @todo organizar con namespaces?
	 *
	 */
	if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', 'public');

	define('HTTP', 'http');
	define('HTTPS', 'https');

	/*var_dump($_SERVER);
	exit;*/

	if (!defined('HOST')) define('HOST', $_SERVER['HTTP_HOST']);

	function get_diff($old, $new)
	{
		$from_start = strspn($old ^ $new, "\0");
		//$from_end = strspn(strrev($old) ^ strrev($new), "\0");

		//$old_end = strlen($old) - $from_end;
		//$new_end = strlen($new) - $from_end;

		$start = substr($new, 0, $from_start);
		/*$end = substr($new, $new_end);
		$new_diff = substr($new, $from_start, $new_end - $from_start);
		$old_diff = substr($old, $from_start, $old_end - $from_start);

		$new = "$start<ins style='background-color:#ccffcc'>$new_diff</ins>$end";
		$old = "$start<del style='background-color:#ffcccc'>$old_diff</del>$end";*/
		return $start;
	}

//if(!defined('BASE_URI')) define('BASE_URI', dirname($_SERVER["PHP_SELF"]));
	if (!defined('BASE_URI')) define('BASE_URI', get_diff($_SERVER['REQUEST_URI'], dirname($_SERVER["PHP_SELF"])));


	if (!defined('SITE_PATH')) define('SITE_PATH', preg_replace('/' . preg_quote(trim(PUBLIC_PATH, '/\\'), '/') . '$/', '', dirname($_SERVER['SCRIPT_FILENAME'])));
	if (!defined('SITE_LIB_PATH')) define('SITE_LIB_PATH', dcc(SITE_PATH, 'lib'));
	if (!defined('SITE_MODELS_PATH')) define('SITE_MODELS_PATH', dcc(SITE_PATH, 'models'));
	if (!defined('URL')) define('URL', @$_SERVER['HTTPS'] ? HTTPS : HTTP . '://' . dcc(HOST, BASE_URI) . URI_SEP);



	define('CRLF', "\r\n");
	define('DEF_CHARSET', 'UTF-8');
	if (function_exists('mb_regex_encoding')) {
		mb_regex_encoding(DEF_CHARSET);
	}
	if (function_exists('mb_internal_encoding')) {
		mb_internal_encoding(DEF_CHARSET);
	}
	if (function_exists('mb_http_output')) {
		mb_http_output(DEF_CHARSET);
	}

	if (DEBUG === true) {
		error_reporting(E_ALL);
	} else {
		error_reporting(0);
	}

// Directorios Estandar
	define('FW_PATH', dirname(__FILE__));
	if (!defined('FW_LIB_PATH')) define('FW_LIB_PATH', dcc(FW_PATH, 'lib'));

	if (!defined('TEMPLATE_EXTENSION')) define('TEMPLATE_EXTENSION', '.tpl');
	if (!defined('CONTROLLER_EXTENSION')) define('CONTROLLER_EXTENSION', '.ctr.php');
	/**
	 * Carga las funciones visibles desde todas partes
	 */

	require_once(dcc(FW_LIB_PATH, 'Functions.php'));


	if(isset($_REQUEST['lang'])){
	    include('lib/iso_lang_array.php');
        $iso_lang_array_keys = array_keys($iso_lang_array);
	    $lang_key = array_search($_REQUEST['lang'], $iso_lang_array_keys);
        if($lang_key !== false) {
            $_SESSION['__PREFERED_LANG'] = $iso_lang_array_keys[$lang_key];
        }
    }


    class Language
    {
        public static function getPreferedLangs(){
            $prefLangs = array();
            if(isset($_SESSION['__PREFERED_LANG'])){
                array_push ($prefLangs, $_SESSION['__PREFERED_LANG']);
            }
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                $langs = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                foreach($langs as $lang) {
                    $lang = explode(',', $lang);
                    foreach ($lang as $k => $l) {
                        if (strpos($l, 'q=') !== false) continue;
                        $ldiv = strpos($l, '-');
                        if ($ldiv !== false) $l = substr($l, 0, $ldiv);
                        array_push($prefLangs, $l);
                    }
                }
            }
            return array_unique($prefLangs, SORT_STRING);
        }
        public static function getPreferedLang(){
            $langs = self::getPreferedLangs();
            if(count($langs) > 0) {
                return self::getPreferedLangs()[0];
            }
            return null;
        }
    }



	/**
	 * Busca los archivos de las clases.
	 *
	 * @param string $class_name
	 * @return boolean
	 */
	class Config
	{
		public static $INCLUDE_PATHS = array();
		private static $DEF_DB = null;

		public static function setDefaultDB(DBContext $context)
		{
			self::$DEF_DB = $context;
		}

		/**
		 * @return DBContext
		 */
		public static function getDefaultDB()
		{
			return self::$DEF_DB;
		}

		public static function setContentType($content_type, $encoding)
		{
			HttpHeaders::setContentType($content_type, $content_type);
		}
	}


	class App
	{
	    private $Router;

	    public function __construct(){
	        $this->Router = new Router($this);
        }

        public function setDefaultDatabase(DBContext $context){
	        Config::setDefaultDB($context);
        }

        public function &getRouter(){
            return $this->Router;
        }

		public function execute(){
            $uri = preg_replace('/'.preg_quote(BASE_URI, '/').'/', '', $_SERVER['REQUEST_URI'], 1);
            $uri = strtok($uri, '?');

            try {
                $response = $this->Router->execute($uri);
            }catch (\Exception $e){
                echo $e->getMessage();
                echo $e->getTraceAsString();
                exit();
            }


            $_SESSION['Router']['Last'] = LocalUrl::current();
            $GLOBALS['SC_TIME'] = (Time::getCurrentTime() - $GLOBALS['SCRIPT_INIT_TIME'] - $GLOBALS['DB_TIME']);

            $body = $response->getBody();
            $headers = $response->getHeaders();
            if(is_null($body)) {

            }else if($body instanceof BaseTemplate){
                if(!defined('DEBUG')) {
                    $Search = array(
                        '/(\n|^)(\x20+|\t)/',
                        '/(\n|^)\/\/(.*?)(\n|$)/',
                        '/\n/',
                        '/\<\!--.*?-->/',
                        '/(\x20+|\t)/', # Delete multispace (Without \n)
                        '/\>\s+\</', # strip whitespaces between tags
                        '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
                        '/=\s+(\"|\')/'); # strip whitespaces between = "'

                    $Replace = array(
                        "\n",
                        "\n",
                        " ",
                        "",
                        " ",
                        "><",
                        "$1>",
                        "=$1");

                    $body = preg_replace($Search, $Replace, $body);
                }
            }else if($body instanceof Json){
                $headers->add(Header::contentType('json'));
                $body = $body->__toString();
            }else if($body instanceof Media){
                $headers->add(Header::contentType($body->getMimeType()));
                $body = $body->__toString();
            }else if($body instanceof Pdf){
                $headers->add(Header::contentType('pdf'));
                HttpHeaders::setContentType('pdf');
                $body = $body->__toString();
            }else if($body instanceof File){
                $headers->add(Header::contentType($body->getMimeType()));
                $headers->add(Header::create('Content-Length',  $body->getSize()));
                $headers->send();
                $body->send();
                flush();
                return;
            }else if($body instanceof Redirect){
                $headers->add(Header::create('Location', $body->getUrl()));
            }else{

            }
            $headers->send();
            echo $body;
            flush();

        }
	}


	function classAutoload($class_name)
	{

		/*if (preg_match('@\\\\([\w]+)$@', $class_name, $matches)) {
		$class_name = $matches[1];
			}*/
		$class_name = str_replace('fw\\', '', $class_name);
		$class_name = str_replace('\\', '/', $class_name);
		///echo $class_name.' ';

		$paths = [FW_LIB_PATH, SITE_LIB_PATH, SITE_MODELS_PATH, FW_LIB_PATH.DIR_SEP.'third-party'];
		$paths = array_merge(Config::$INCLUDE_PATHS, $paths);

		$filename = $class_name . '.php';
		foreach ($paths as $p) {
		    //var_dump(dcc($p, $filename));
			if (file_exists($class_path = dcc($p, $filename))) {
				include_once($class_path);
				return;
			}
		}

		//throw new \Exception("Class not found in '$filename' <br/>" . implode("<br/>", $paths)."<br/><br/>");
	}

	function argsToString($args)
	{
		$out = '';
		/*if(isset($args) && is_array($args)){
			foreach($args as $a){
				if(is_object($a)){
					$out .= get_class($a);
				}else{
					$out .= "'".substr(preg_replace('/\s\s+/','\n' ,$a) ,0,20)."'";
				}
				$out .= ', ';
			}
		}*/
		return $out;
	}

	function traceToString($trace){
		$out = '';
		implode(",", []);
		foreach ($trace as $key => $value) {

			$out .= "<strong>[$key]</strong> $value[function]() on line <strong>" . @$value['line']."</strong>, ".@$value['file']."\n";
		}
		return $out;
	}

	function errorHandler()
	{
	    $error = error_get_last();

        if (DEBUG) {

            echo "<pre style=\"line-height:20px;text-align:left;margin:0;padding:0;font-size:14px;font-weight:200;color:red;background:#fff;padding:40px;width:100%\"><strong>Fatal Error:</strong>
		{$error['message']}
		{$error['file']} on line <strong>{$error['line']}</strong>";
        debug_print_backtrace();

		
		echo "</pre>";
        }

        exit();
	}

	function exceptionHandler($e)
	{
	    /*if($e instanceof HttpCodeException){
	        if($template = $e->getTemplate()){
                echo $template->render();
                exit();
            }
        }*/
		if (DEBUG) {
			$trace = traceToString($e->getTrace());


			exit("<pre style=\"line-height:20px;text-align:left;margin:0;padding:0;font-size:14px;font-weight:200;color:red;background:#fff;padding:40px;width:100%\"><strong>Fatal Error:</strong>
<strong>{$e->getMessage()}</strong>
{$e->getFile()} on line <strong>{$e->getLine()}</strong>
		
{$trace}</pre>");
		}
        exit();
	}
}

namespace {

	use function fw\classAutoload;

    spl_autoload_register ('fw\classAutoload');
	/*function __autoload($class_name)
	{
		classAutoload($class_name);
	}*/

    //register_shutdown_function('\fw\errorHandler');
	//set_error_handler('\fw\errorHandler');
	//set_exception_handler('\fw\exceptionHandler');

}


?>