<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

include "config.php";
require_once 'librerias_backend/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('vistas');
$twig = new Twig_Environment($loader);

require_once 'modelo/Utilidades.php';

$datos['canales'] = [];
$qry_todos_canales=Utilidades::getAllCanal();
foreach ($qry_todos_canales as $canal)
{
    $canal['logo'] = "../logos/".strtolower(str_replace(" ", "-", $canal['nombre']).'-['.$canal['calidad'].']').".png";
    array_push($datos['canales'], $canal);
}

if(count($datos['canales']) > 10){
    $datos['multi_table'] = true;
    
    $a = intval(count($datos['canales'])/2);
    $i = 0;
    $datos['tabla_a'] = [];
    foreach($datos['canales'] as $canal){
        $canal_n = explode(" [", $canal['nombre']);
        $canal['nombre'] = $canal_n[0];
        array_push($datos['tabla_a'], $canal);
        $i++;
        if($i >= $a)
            break;
    }
    $datos['tabla_b'] = [];
    $i = 0;
    foreach($datos['canales'] as $canal){
        if($i >= $a){
            $canal_n = explode(" [", $canal['nombre']);
            $canal['nombre'] = $canal_n[0];
            array_push($datos['tabla_b'], $canal);
            $a++;
        }
        $i++;
        if($a >= $datos['canales'])
            break;
    }
} else {
    $datos['multi_table'] = false;
}

echo $twig->render('index.html', array('datos'=>$datos));
