<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("./AuthRestHandler.php");
require_once("./MCrypt.php");
$_POST = json_decode(file_get_contents('php://input'), true);

/*$_POST['q']="channels";
$_POST['tk']="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE1NzI1NjM5MjMsImF1ZCI6IjNjMGUxYzUxZjFmYWMzYjhiZTI1NTYwYTE1YmFlYWEwYWE4NzZlOTQiLCJkYXRhIjp7InVzZXJpZCI6Ijg2OTM3MDAyNjUwNTM5MyJ9fQ.7ATxSnktKbed6cCx99V2hVnIiiNaNPXzRYTfxOwKAGI";*/

$q = "";
if(isset($_POST["q"]))
	$q = $_POST["q"];

switch($q){

	case "client":
		$authRestHandler = new AuthRestHandler();
		$authRestHandler->getToken($_POST);
		//$authRestHandler->getClient($_POST);
		break;
	case "channels":
		$authRestHandler = new AuthRestHandler();
		$authRestHandler->getClient($_POST);
		break;

	case "messages":
		$authRestHandler = new AuthRestHandler();
		$authRestHandler->getMensajesDispo($_POST);
		break;

	case "messagesview":
		$authRestHandler = new AuthRestHandler();
		$authRestHandler->setVistoMensajesDispo($_POST);
		break;

	case "version":
		$authRestHandler = new AuthRestHandler();
		$authRestHandler->getVersion($_POST);
		break;

	case "" :
		//404 - not found;
		break;
}



function decryptData($data){
	if (isset($data['tk'])) {
		$mcrypt = new MCrypt();
		$tk = $mcrypt->decrypt($data['tk']);
		$data_tk = explode("___", $tk);
		

	}
	return false;
}
?>
