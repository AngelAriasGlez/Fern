<?php
namespace fw;

class DBContext{
	private $_pdo;
	public function __construct($dbname, $user, $pass, $host = '127.0.0.1'){
		


		try{
			$this->_pdo = new \PDO('mysql:host='.$host.';dbname='.$dbname.';charset=utf8', $user, $pass);
			$this->_pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
		}
		catch( \PDOException $e ) {
			throw new \Exception( $e->getMessage( ) . ' '. $e->getCode( ) );
		}
		
	}
	
	public function query($query){
		if (DEBUG){
			$startime = Time::getCurrentTime();
		}
		$res = $this->_pdo->query($query);
		if (DEBUG){
			$t = Time::getCurrentTime() - $startime;
			InfoBar::setDbQuery($t, $query);
			$GLOBALS['DB_TIME'] += $t; // Poco elegante
		}
		return $res;
	}
	public function prepare($query){
		return $this->_pdo->prepare($query);
	
	}
	public function exec($query){
		if (DEBUG){
			$startime = Time::getCurrentTime();
		}
		$res = $this->_pdo->exec($query);
		if (DEBUG){
			$t = Time::getCurrentTime() - $startime;
			InfoBar::setDbQuery($t, $query);
			$GLOBALS['DB_TIME'] += $t; // Poco elegante
		}

		return $res;
	
	}
	public function lastInsertId($query){
		return $this->_pdo->lastInsertId($query);
	
	}
	public function errorInfo(){
		return $this->_pdo->errorInfo();
	}
	public function commit(){
		return $this->_pdo->commit();
	}
}



?>