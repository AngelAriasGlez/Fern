<?php
/**
 *Construye una tabla ascii
 */
class asciiTable {
	const ALIGN_RIGHT = STR_PAD_LEFT;
	const ALIGN_CENTER = STR_PAD_BOTH;
	const ALIGN_LEFT = STR_PAD_RIGHT;
	const DIVISOR = 5;
	private $_cols = array();
	private $_values = array();
	private $_align;
	private $_border;
	/**
	 * @param array ColumnAlias=>ColunName
	 */
	public function __construct(array $cols, $align = self::ALIGN_LEFT, $border = false){
		$this->_cols = $cols;
		$this->_align = $align;
		$this->_border = $border;
	}
	/**
	 * Add table row
	 *
	 * @param array ColumnAlias=>Value
	 */
	public function addRow($values){
		if(count($values) > count($this->_cols)){
			throw new Exception(500,"Sql Problem",false,'Too columns '.__FILE__);
		}
		if($values != self::DIVISOR)
		foreach($values as $col=>$value){
			if(empty($this->_cols[$col])){
				throw new Exception(500,"SQl Problem",false,'Column "'.$col.'" not found');
			}
		}
		$this->_values[] = $values;
	}
	private function colsLen(){
		$cl = array();
		foreach ($this->_cols as $colKey=>$name){
			$mlen = 0;
			foreach($this->_values as $row){
				if(($len = mb_strlen($row[$colKey], 'utf-8')) > $mlen){
					$mlen = $len;
				}
			}
			if(($nlen = mb_strlen($name, 'utf-8')) > $mlen){
				$mlen = $nlen;
			}
			$cl[$colKey] = $mlen;
		}
		return $cl;
	}
	/**
	 * Return table charater width
	 * @return int
	 */
	public function getTableWidth(){
		$sum = (int)array_sum($this->colsLen())+(count($this->_cols)*3);
		if($this->_border){
			$sum++;
		}else{
			$sum--;
		}
		return $sum;
	}
	public function __toString(){
		$colsLen = $this->colsLen();

		/***sep***/
		$sep = '+';
		foreach($colsLen as $l){
			$sep .= str_repeat('-', $l+2).'+';
		}
		$sep .= "\r\n";
		if(!$this->_border) $sep = '';
		/****head****/
		$out = '';
		$out .= $sep;
		if($this->_border)$out .= '| ';
		foreach ($this->_cols as $colKey=>$name){
			$out .= mb_strpad($name, $colsLen[$colKey], ' ', STR_PAD_BOTH);
			if($this->_border)
				$out .= ' | ';
			else
				$out .= str_repeat(' ', 3);
		}
		$out = mb_substr($out,0, mb_strlen($out)-1, 'utf-8');
		$out .= "\r\n";
		$out .= $sep;

		/****rows****/
		foreach ($this->_values as $row){
			if($row == self::DIVISOR){
				$out .= $sep;
			}else{
				if($this->_border)$out .= '| ';
				foreach ($row as $colKey=>$value){

					$out .= mb_strpad($value, $colsLen[$colKey], ' ', $this->_align);
					if($this->_border && !is_null($value))
						$out .= ' | ';
					else
						$out .= str_repeat(' ', 3);
				}
			$out = mb_substr($out,0, mb_strlen($out)-1, 'utf-8');
			$out .= "\r\n";
			}
		}
		return $out.$sep;
	}

}
function mb_strpad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = null){
	if(!$encoding) $encoding = mb_internal_encoding();
	$len = mb_strlen($input, $encoding);
	if($len < $pad_length){
		$pad = $pad_length - $len;
		switch ($pad_type){
			case STR_PAD_BOTH:
				return str_repeat($pad_string, floor($pad/2)).$input.str_repeat($pad_string, ceil($pad/2));
			case STR_PAD_LEFT:
				return str_repeat($pad_string, $pad).$input;
			case STR_PAD_RIGHT:
				return $input.str_repeat($pad_string, $pad);
		}
	}
	return $input;
}
?>