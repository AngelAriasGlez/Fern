<?php
/**
 * Created by PhpStorm.
 * User: Angel
 * Date: 27/04/2017
 * Time: 1:44
 */

namespace fw;


class DataValidator
{
	const NUMBER = "/^[0-9]*$/";
	const INT = self::NUMBER;
	const LONG = self::NUMBER;
	const DECIMAL = "/^[0-9-.,]*$/";
	const DOUBLE = self::DECIMAL;
	const FLOAT = self::DECIMAL;
	const ALPHA_NUMERIC = "/^[a-zA-Z0-9]*$/";
	const STRING = "/^.*$/s";
	const ALL = self::STRING;
	const BOOL = "/^(?i)(tru|fals)e$/";
	const SEARCH_QUERY = "/^[a-zA-Z ]*$/";
	const IID = self::NUMBER;
	const GOOGLE_PLACE_ID = "/^[a-zA-Z0-9-]*$/";
	const ARRAY = 1;
	const ENUM = 2;
	const JSON = 3;

	const MIN_NUM = 81;
	const MAX_NUM = 80;

	const DEF = 90;




    const EMAIL = 55;

	private $_rules = [];



	public function __construct()
	{

	}

	public function sanitize($data){
		if($data === null) $res = $this->getDefault();
		$res = $this->process($data);
		if($res === null) $res = $this->getDefault();
		return $res;
	}
	public function isValid($data){
		if($this->process($data) === null) return false;
		return true;
	}

	private function process($data){
		foreach($this->_rules as $sf) {
			$rule = $sf[0];
			if($rule == self::DEF || $rule == self::MIN || $rule == self::MAX) continue;

			$param = $sf[1];

			if (is_callable($rule)) {
				$data = call_user_func($rule, $data, null, $param);
			} elseif ((is_array($rule) || ($rule == self::ENUM && is_array($rule)))) {
				if (array_search($data, $rule) === FALSE) $data = null;
			}elseif ($rule == self::JSON) {
                $data = json_decode(filter_var($data, FILTER_SANITIZE_STRING));
            }elseif ($rule == self::EMAIL) {
				$data = self::validateEmail($data);
			} else {
				switch ($rule) {
					case self::ARRAY:
						$data = is_array($data) ? $data : null;
						break;
					default:
						preg_match((string)$rule, $data, $matches);
						//echo $rule;
						$data = isset($matches[0]) && !empty($matches[0]) ? $matches[0] : null;
				}
			}

		}
		$max = $this->getRule(self::MAX_NUM);
		if($max && (strlen($data) > intval($max[1]))) $data = null;
		$min = $this->getRule(self::MIN_NUM);
		if($min && (strlen($data) < intval($min[1]))) $data = null;
		return $data;
	}



	public function add($rule, $param = null){
		$this->_rules[] = [$rule, $param];
		return $this;
	}
	public function max($m){
		$this->add(self::MAX, $m);
		return $this;
	}
	public function min($m){
		$this->add(self::MIN, $m);
		return $this;
	}
	public function def($d){
		$this->add(self::DEF, $d);
		return $this;
	}
	public function number(){
        $this->add(self::NUMBER);
    }

	private function getRule($f){
		$d = array_search($f, array_column($this->_rules, 0));
		if($d === false){
			return null;
		}else{
			return $this->_rules[$d];
		}
	}

	public function getDefault(){
		$d = $this->getRule(self::DEF);
		if($d === false){
			return null;
		}else{
			return $d[1];
		}
	}
/*
	public function isValid(){
		if(!isset($this->_data)){
			return false;
		}
		$this->filter();

		if(empty($this->_data)){
			return false;
		}


		return true;
	}
*/


    public static function validateEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        if (!empty($email))
        {
            $domain = ltrim(stristr($email, '@'), '@');
            $user   = stristr($email, '@', TRUE);

            if
            (
                !empty($user) &&
                !empty($domain) &&
                checkdnsrr($domain)
            )
            return $email;
        }

        return null;
    }
}
