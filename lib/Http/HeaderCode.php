<?php
/*
 * Retorna un Código HTTP
 */
namespace fw\Http;
class HeaderCode{

	const CONTINUE_ = 100; // The Client request has not been rejected yet.
	const SWITCHING_PROTOCOLS = 101;// Changing protocol by client request on server HTTP/1.0 => HTTP/1.1
	
	const OK = 200;// Request Succeeded
	const CREATED = 201;// Server finish the creation of resource, new resource with Etag can send with Location
	const ACCEPTED = 202;// Server is processing request, need estimate time to left
	const NON_AUTHORITATIVE_INFORMATION = 203;	// This resourse is like origin server but not it is original.
	const NO_CONTENT = 204;// Server fullfilled action, Client should not change document view and continue with more Data. No body. Metadata it is applied
	const RESET_CONTENT = 205;// Server fullfilled action. And Client Should request again the same resource.
	const PATIAL_CONTENT = 206;// Server fullfilled partial GET request. Partial resource has been send
	
	const MULTIPLE_CHOICES = 300;// Multiple choices. Response Including Location, Content-Type of each resource. Client select one. Server can have preferred.and client may use it.
	const MOVED_PERMANENTLY = 301;// The request URI has permanenly moved to the resource located on Location, methos only GET and HEAD
	const FOUND = 302;// Temporaly, resource resides under diffrent URI in Location. Agent must not automatically redirect.
	const SEE_OTHER = 303;// After proccess request (POST action), Client should see other page. The new page do not replace the first requested uri.
	const NOT_MODIFIED = 304;// the requested document has not been modified. response Must include  Date, Etag,Expires, Cache-Control,...
	const USE_PROXY = 305;// All The request should be performed using a proxy located in the Location
	const TEMPORARY_REDIRECT = 305;//Temporaly redirect, for the requested URI, redirection resource is in Location
	
	const BAD_REQUEST = 400;// Server can not understood the request
	const UNAUTHORIZED = 401;// Authentication is required with WWW-Authenticate method
	const PAYMENT_REQUIRED = 402;// Unused
	const FORBIDDEN = 403;// Server understood request, but result if forbidden without any prerrequisite.
	const NOT_FOUND = 404;//  No resource is avaliable to the client.
	const METHOD_NOT_ALLOWED = 405;// Request methos is not allowed, response should include valid methods
	const NOT_ACCEPTABLE = 406;// The resource is only capable of generating entities wiich have content characteristics not acceptable. Alternate locations and content-type  should be send by the server.
	const PROXY_AUTHENTICATION_REQUIRED = 407;// Client should perform autentification on the proxy
	const REQUEST_TIMEOUT = 408;// Client do not response at a time to the server
	const CONFLIT = 409;// Request URI make a conflict beteween resources. Server should send information to resolve it.
	const GONE = 410;// The resource is no longer available on the server.
	const LENGTH_REQUIRED = 411;// Server need know the Content-Length
	const PRECONDITION_FAILED = 412;// Client preconditions have been tested to false.
	const REQUEST_ENTITY_TOO_LARGE = 413;// Entity is too long to the server
	const REQUEST_URI_TOO_LARGE = 414;// Request URI is too large
	const UNSUPORTED_MEDIA_TYPE = 415;// Response is not suported by the request format
	const REQUEST_RANGE_NOT_SATISFIABLE = 416;// Range can not fullfilled by server
	const EXPECTATION_FAILED = 417;// request header field could not be met by this server
	
	const INTERNAL_SERVER_ERROR = 500;// Unexpected condition, request not fullfilled.
	const NOT_IMPLEMENTED = 501;// Request method is not supported
	const BAD_GATEWAY = 502;// Server acting as proxy received an invalid response
	const SERVICE_UNAVAILABLE = 503;// server overloading-maintenance, Retry-After: Seconds
	const GATEWAY_TIMEOUT = 504;// server acting as gateway did not receive response
	const VERSION_NOT_SUPORTED = 505;// server does not support, or refuses to support HTTP protocol



	public static function getMessageWithCode($code){
		switch ($code) {
			case 100: $text = 'Continue'; break;
			case 101: $text = 'Switching Protocols'; break;
			case 200: $text = 'OK'; break;
			case 201: $text = 'Created'; break;
			case 202: $text = 'Accepted'; break;
			case 203: $text = 'Non-Authoritative Information'; break;
			case 204: $text = 'No Content'; break;
			case 205: $text = 'Reset Content'; break;
			case 206: $text = 'Partial Content'; break;
			case 300: $text = 'Multiple Choices'; break;
			case 301: $text = 'Moved Permanently'; break;
			case 302: $text = 'Moved Temporarily'; break;
			case 303: $text = 'See Other'; break;
			case 304: $text = 'Not Modified'; break;
			case 305: $text = 'Use Proxy'; break;
			case 400: $text = 'Bad Request'; break;
			case 401: $text = 'Unauthorized'; break;
			case 402: $text = 'Payment Required'; break;
			case 403: $text = 'Forbidden'; break;
			case 404: $text = 'Not Found'; break;
			case 405: $text = 'Method Not Allowed'; break;
			case 406: $text = 'Not Acceptable'; break;
			case 407: $text = 'Proxy Authentication Required'; break;
			case 408: $text = 'Request Time-out'; break;
			case 409: $text = 'Conflict'; break;
			case 410: $text = 'Gone'; break;
			case 411: $text = 'Length Required'; break;
			case 412: $text = 'Precondition Failed'; break;
			case 413: $text = 'Request Entity Too Large'; break;
			case 414: $text = 'Request-URI Too Large'; break;
			case 415: $text = 'Unsupported Media Type'; break;
			case 500: $text = 'Internal Server Error'; break;
			case 501: $text = 'Not Implemented'; break;
			case 502: $text = 'Bad Gateway'; break;
			case 503: $text = 'Service Unavailable'; break;
			case 504: $text = 'Gateway Time-out'; break;
			case 505: $text = 'HTTP Version not supported'; break;
			default:
				$text = 'Unknown http status code "' . htmlentities($code) . '"';
				break;
		}
		return $text;
	}

	private $Code = null;
	public function __construct($code = 200){
		$this->Code = $code;
	}

	public function getMessage(){
	    return self::getMessageWithCode($this->Code);
    }

    public static function create($code){
        return new self($code);
    }

	public function send(){
	    header($_SERVER['SERVER_PROTOCOL'].' '.$this->Code.' '.$this->getMessage(), true, $this->Code);
	}






}
?>