<?php
require_once __DIR__ . '/config/conexion.php';

// retorna un objeto de la clase Conexion con la conexion a y mysql iniciada
function getConexion(){
    return new Conexion("localhost", "root", "12345", "db_test");
}

function validarInfo($infoObj, $propiedadesRequeridas = []){
    $messages = [];
    foreach($propiedadesRequeridas as $propiedad){
        if(!isset($infoObj->{$propiedad}) || empty($infoObj->{$propiedad})){
            array_push($messages, "la propiedad $propiedad es requerida");
        }
    }
    return $messages;
}
function validarApi($json, $methodPermitido, $validarJson = true, $validarIdGet=false){
    $method = $_SERVER['REQUEST_METHOD'];
    if($method != $methodPermitido){
        return 'Solo se soporte metodo ' . $methodPermitido;
    } else if($validarJson && $json == null){
        return 'request json invalido';
    }
    if(!$validarIdGet) return null;
    
    if(!isset($_GET) || empty($_GET["id"]) || $_GET['id'] == "0"){
        return 'id requerido y debe ser diferente de 0';
    }
}