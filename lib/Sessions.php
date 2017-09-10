<?
class Sessions extends fwActiveShm {
	
	
	
	public function __construct(){
		parent::__construct('Sessions',array('lastActiveTime','badPoints','cookiesCount'),true,2678400);// Un mes
	}
	
	
	
	/**
	 * Comprueba si se han asignado más del número de sesiones permitido
	 *
	 * @return unknown
	 */
	public function is_exceeded() {
		//$this->SessionID=session_id();
		$this->setId($_SERVER['REMOTE_ADDR']);
		$time = time();
		if (($time - (24 * 3600)) < $this->lastActiveTime){
			$count = $this->cookiesCount + 1; // Cookies asignadas para esa IP
			$this->cookiesCount=$count;
		}else{
			$this->cookiesCount=0;
		}
		$this->lastActiveTime=$time;
		$this->save();
		if (isset($count) && ($count > 40)){
			return true;
		}else{
			return false;
		}
	}
}
/**

CREATE TABLE "Sessions"
(
"IP" character varying(128) NOT NULL,
"SessionID" character varying(128),
"wrongLoginCount" integer,
"lastActiveTime" integer NOT NULL,
"badPoints" integer DEFAULT 0,
CONSTRAINT "IP" PRIMARY KEY ("IP")
)
WITH (OIDS=FALSE);
ALTER TABLE "Sessions" OWNER TO root;
*/
?>