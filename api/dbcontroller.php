<?php
class DBController {

	function __construct() {
		$conBD = $this->connectDB();
		if(!empty($conBD)) {
			$this->conBD = $conBD;
		}
	}

	function connectDB() {
		if(!class_exists('BDManager'))
		  require_once $_SERVER['DOCUMENT_ROOT'].'/modelo/BDManager.php';
	  	$conBD = BDManager::getBDManager()->getConectionBD();
		return $conBD;
	}

	function executeSelectQuery($qry,$fetch) {
		$query=$this->conBD->prepare($qry);
		$query->execute();
		if(!empty($fetch))
	    	$resultset=$query->fetch(PDO::FETCH_ASSOC);
	    else
	    	$resultset=$query->fetchAll(PDO::FETCH_ASSOC);
		if(!empty($resultset))
			return $resultset;
	}

	function executeUpdateQuery($qry,$fetch) {
		$query=$this->conBD->prepare($qry);
		$query->execute();
	}
}
?>
