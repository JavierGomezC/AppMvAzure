<?php
require_once("dbcontroller.php");
/* 
A domain Class to demonstrate RESTful web services
*/
Class Auth {
	private $auth = array();
	/*
		you should hookup the DAO here
	*/
	public function getValidClient($uid){
		$query = "SELECT * FROM dispositivo INNER JOIN dispositivo_plan ON dispositivo.id=dispositivo_plan.id_dispositivo WHERE dispositivo.sn='".$uid."'";
		$dbcontroller = new DBController();
		$this->mobiles = $dbcontroller->executeSelectQuery($query,true);
		return $this->mobiles;
	}
	public function getChannelListByPlan($id){
		$query = "SELECT * FROM plan_canal INNER JOIN canales ON plan_canal.id_canal=canales.id WHERE plan_canal.id_plan=".$id." ORDER BY canales.numero ASC";
		$dbcontroller = new DBController();
		$this->mobiles = $dbcontroller->executeSelectQuery($query,false);
		return $this->mobiles;
	}
	public function getMessagesByDisp($id){
		$query = "SELECT * FROM dispositivo_mensaje INNER JOIN mensaje ON dispositivo_mensaje.id_mensaje=mensaje.id WHERE dispositivo_mensaje.id_dispositivo=".$id." AND dispositivo_mensaje.visto=0";
		$dbcontroller = new DBController();
		$this->mobiles = $dbcontroller->executeSelectQuery($query,false);
		return $this->mobiles;
	}
	public function setViewMessagesByDisp($id_mensa, $id_dispo){
		$query = "UPDATE dispositivo_mensaje SET dispositivo_mensaje.visto=1 WHERE dispositivo_mensaje.id_mensaje=".$id_mensa." AND dispositivo_mensaje.id_dispositivo=".$id_dispo;
		$dbcontroller = new DBController();
		$this->mobiles = $dbcontroller->executeUpdateQuery($query,true);
		return $this->mobiles;
	}
	public function getVersion(){
		$query = "SELECT aplicativo.version FROM aplicativo WHERE aplicativo.id=1";
		$dbcontroller = new DBController();
		$this->mobiles = $dbcontroller->executeSelectQuery($query,false);
		return $this->mobiles;
	}
}
?>