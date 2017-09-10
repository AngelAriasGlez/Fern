<?php
namespace fw;
class Time {
	/**
	 * Devulve la cadena formateada a tiempo HTTP
	 *
	 * @param timestamp $Time
	 * @return string
	 */
	public static function httpDate($Time){
		return gmdate("D, d M Y H:i:s", $Time) . ' GMT';
	}

	/**
 * Devuleve la suma de segundos y microsegundos
 *
 * @return unknown
 */
	public static function getCurrentTime(){
		list($useg, $seg) = explode(' ', microtime());
		return $useg + $seg;
	}
}
?>