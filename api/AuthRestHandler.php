<?php
require_once("./SimpleRest.php");
require_once("./Auth.php");
require_once("./Token.php");
if(!class_exists('../modelo/Utilidades'))
  require_once '../modelo/Utilidades.php';

class AuthRestHandler extends SimpleRest {

	function getAllAuths() {	

		$mobile = new Auth();
		$rawData = $mobile->getAllMobile();

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'No mobiles found!');
		} else {
			$statusCode = 200;
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
		
		$result["output"] = $rawData;
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($result);
			echo $response;
		}
	}
	function getClient($data) {	


		$token = new AuthTK();
		$auth = new Auth();
		$valid = $token->Check($data['tk']);

		if(empty($valid)) {
			$statusCode = 404;
		} else {
			$statusCode = 200;
			$dataTK = $token->GetData($data['tk']);
			$user = $auth->getValidClient($dataTK->userid);
			$canales = $auth->getChannelListByPlan($user['id_plan']);
			$lista = $this->generateChannelList($canales);
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($lista);
			echo $response;
		}
	}
	function getToken($data) {	
		$token = new AuthTK();
		$tk_data = $this->decryptData($data['tk']);
		if($tk_data===false){
			var_dump("invalido"); die;
		}
		$tk_row = explode("___", $tk_data);
		$info_tk=array('userid' => $tk_row[0]);
		$rawData = $token->SignIn($info_tk);

		if(empty($rawData)) {
			$statusCode = 404;
			$rawData = array('error' => 'Usuario no es valido!');		
		} else {
			$statusCode = 200;
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
		
		$result["output"] = $rawData;
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($result);
			echo $response;
		}
		
	}
	


	public function generateChannelList($data){
		$lista=array();
		foreach ($data as &$valor) {
			$canonical=Utilidades::normalizarTexto($valor['nombre']);
			$arrCH = array('programas' => array(),'id' => $valor['id'], 'num' => $valor['numero'],'name' => $valor['nombre'],'logo' => $valor['logo'],'categoria' => $valor['categoria'],'url' => $valor['url']);
			array_push($lista, $arrCH);
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$statusCode=200;
		$this ->setHttpHeaders($requestContentType, $statusCode);
					
		if(strpos($requestContentType,'application/json') !== false){
			$response = array('data' => $lista);
			return $response;
		}
		return false;
	}


	public function decryptData($value){
		if (isset($value)) {
			$mcrypt = new MCrypt();
			$tk = $mcrypt->decrypt($value);
			return $tk;
		}
		return false;
	}

	public function encryptData($value){
		if (isset($value)) {
			$mcrypt = new MCrypt();
			$tk = $mcrypt->encrypt($value);
			return $tk;
		}
		return false;
	}
	public function encodeJson($responseData) {
		$jsonResponse = json_encode($responseData);
		return $jsonResponse;		
	}


	function getMensajesDispo($data) {	
		$token = new AuthTK();
		$auth = new Auth();
		$valid = $token->Check($data['tk']);

		if(empty($valid)) {
			$statusCode = 404;
		} else {
			$statusCode = 200;
			$dataTK = $token->GetData($data['tk']);
			$user = $auth->getValidClient($dataTK->userid);
			$mensajes = $auth->getMessagesByDisp($user['id_dispositivo']);
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson(array('data'=>$mensajes));
			echo $response;
		}
	}

	function setVistoMensajesDispo($data) {	
		$token = new AuthTK();
		$auth = new Auth();
		$valid = $token->Check($data['tk']);

		if(empty($valid)) {
			$statusCode = 404;
		} else {
			$statusCode = 200;
			$mensajes = $auth->setViewMessagesByDisp($data['idmensaje'], $data['iddispo']);
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson(array('data'=>$mensajes));
			echo $response;
		}
	}

	function getVersion($data) {	
		$token = new AuthTK();
		$auth = new Auth();
		$valid = $token->Check($data['tk']);

		if(empty($valid)) {
			$statusCode = 404;
		} else {
			$statusCode = 200;
			$dataTK = $token->GetData($data['tk']);
			$user = $auth->getValidClient($dataTK->userid);
			$version = $auth->getVersion();
		}

		$requestContentType = 'application/json';//$_POST['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson(array('data'=>$version));
			echo $response;
		}
	}

}
?>