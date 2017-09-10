<?php
namespace fw;
class InfoBar {
	private static $_elements = array();
	private static $_dbq = array();
	public static function toString(){
		if(DEBUG){
			$elements = array();

			usort(self::$_dbq, function($a, $b) {
                if($b['time'] < $a['time']) return -1;
                if($b['time'] > $a['time']) return 1;
                return 0;
			});

			foreach(@self::$_dbq as $e){
				$elements[] = "<div style=''>".number_format($e['time'], 5)." $e[query]</div>";
			}
			//vd(self::$_dbq);
			
			return '
			<div style="position:fixed;width:100%;bottom:0px;right:0px;font-family:Tahoma;font-size:10px;background:#ccc;z-index:99999" id="infobar">
				<div style="margin-left:10px;">
					<div><span style="float:right;margin-right:20px"><a href="javascript:hideinfobar()">Hide</a></span><span style="font-weight:bold;"> fern Framework Instant Window</span></div>
					<div style="overflow:scroll; height:200px;background: #F9F9F9;margin-top:2px;border-top:1px #CCC dotted;padding:4px;">
					<div> 
					<span style="margin-right:10px;">'.date('H:i:s').' </span>
					<span style="margin-right:10px;">PHP: '.number_format($GLOBALS['SC_TIME'], 4) . '</span>
					<span style="margin-right:10px;">DB: '.number_format($GLOBALS['DB_TIME'], 4).'</span>
					<span style="margin-right:10px;">DBCOUNT: '.count(self::$_dbq).'</span>
					<span style="margin-right:10px;">TOTAL: <span style="color:red">'.number_format($GLOBALS['SC_TIME'] + $GLOBALS['DB_TIME'], 4).'</span></span>
					 </div>'.
					
					implode('', $elements).'
							
					</div>
				</div>
			</div>
			<script type="application/javascript">
				function hideinfobar(){
	        		var ib = document.getElementById("infobar");
	        		ib.innerHTML = "";
	        	}
			</script>
			';
		}
		//filter:alpha(opacity=30);-moz-opacity: 0.3;opacity: 0.3;
		return '';
	}
	public static function setElement($text, $color='black'){self::$_elements[] = array($text, $color);}

	public static function setDbQuery($time, $q){
		self::$_dbq[] = ['time'=>$time, 'query'=>$q];
	}
}

?>