<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/servicios/usuarioService.php';
header("Content-Type: application/json");
$response = new stdClass;
$response->code = -1;
$response->message = [];

// obtiene los datos de entrada y los convierte de json a un objeto modificable
$json_text = file_get_contents('php://input');
$json = json_decode($json_text, false);
$error = false;

// validacion del api metodo que sea DELETE y formato de json valido
$errorApi = validarApi($json, "POST");
if(!empty($errorApi)){
    array_push($response->message, $errorApi);
    echo json_encode($response);
    return;
}

// validacion de email y password que esten presentes

$error_messages = validarInfo($json, ['nombre', 'email', 'password', 'confirm_password']);

if($error_messages != null && count($error_messages) > 0){
    array_push($response->message, ...$error_messages);
    $error = true;
} else if(!filter_var($json->email, FILTER_VALIDATE_EMAIL)){
    array_push($response->message, "email debe tener un formato valido ej: example@example.com");
    $error = true;
} else if($json->password != $json->confirm_password){
    array_push($response->message, "password y confirm_password deben ser iguales");
    $error = true;
}

// validacion de que el email no este registrado en db
$userService = new UsuarioService();
if(!$error){
    $exists = $userService->exist_email($json->email);
    if($exists){
        array_push($response->message, "El email ya se encuentra registrado");
        $error = true;
    }
}
// si hay errores los muestra en pantalla en formato json
if($error){
    echo json_encode($response);
    return;
}

// crear el nuevo registro y valida que sea exitoso
$result = $userService->insertar_registro($json);
if(!$result){
    array_push($response->message, 'Hubo un problema al realizar el registro');
} else {
    $response->code = 0;
    array_push($response->message, 'Registro de nuevo usuario satisfactorio');
}
echo json_encode($response);