<?php 
set_time_limit(0);
include "info_canales.php";
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

      if($_POST['accion'] == "crearCanal")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;

        $sql = Utilidades::crearCanal($_POST);
        
        if($sql != null){
          $data = array(
            'ok' => "Canal Creado"
          );
        }else{
          $data = array(
            'err' => "Error"
          );
        }

        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "editarCanal")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;

        $sql = Utilidades::editarCanal($_POST);
        
        if($sql != null){
          $data = array(
            'ok' => "Canal Editado"
          );
        }else{
          $data = array(
            'err' => "Error"
          );
        }

        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "eliminarCanal")
      {
        require_once '../modelo/Utilidades.php';
        $error=true;

        $sql = Utilidades::eliminarCanal($_POST);
        
        if($sql != null){
          $data = array(
            'ok' => "Canal Eliminado"
          );
        }else{
          $data = array(
            'err' => "Error"
          );
        }

        header('Content-Type: application/json');
        echo json_encode($data); 
      }
      else if($_POST['accion'] == "getCanales")
      {
          require_once '../modelo/Utilidades.php';

          $canales=Utilidades::getAllCanal();

          $data = array(
            'ok' => "Ok",
            'canales' => $canales
          );
      
          header('Content-Type: application/json');
          echo json_encode($data);
      }
      
 ?>

