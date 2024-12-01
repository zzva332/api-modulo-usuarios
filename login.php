<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/servicios/usuarioService.php';
header("Content-Type: application/json");
$response = new stdClass;
$response->code = -1;
$response->message = [];
$response->data = null;

// obtiene los datos de entrada y los convierte de json a un objeto modificable
$json_text = file_get_contents('php://input');
$method = $_SERVER['REQUEST_METHOD'];
$json = json_decode($json_text, false);
$error = false;

// validacion de que si tenga formato json
$errorApi = validarApi($json, "POST");
if(!empty($errorApi)){
    array_push($response->message, $errorApi);
    echo json_encode($response);
    return;
}

// validacion de email y password que esten presentes
$error_messages = validarInfo($json, ['email', 'password']);

if($error_messages != null && count($error_messages) > 0){
    array_push($response->message, ...$error_messages);
    $error = true;
}

// si hay errores los muestra en pantalla en formato json
if($error){
    echo json_encode($response);
    return;
}


// se valida el usuario y password para mirar si existe  y sino mostrar mensaje
$userService = new UsuarioService();
$result = $userService->get_by_credenciales($json->email, $json->password);
if($result == null){
    array_push($response->message, 'usuarios y password invalidos');
} else {
    $response->code = 0;
    array_push($response->message, 'login es satisfactorios');
    $response->data = $result;
    unset($response->data->password);
}
echo json_encode($response);