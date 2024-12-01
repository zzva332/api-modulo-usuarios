<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/servicios/usuarioService.php';
header("Content-Type: application/json");
$response = new stdClass;
$response->code = -1;
$response->message = [];

// obtiene los datos de entrada y los convierte de json a un objeto modificable
$method = $_SERVER['REQUEST_METHOD'];
$json_text = file_get_contents('php://input');
$json = json_decode($json_text, false);
$error = false;

// validacion de que si tenga formato json
$errorApi = validarApi($json, "PUT", validarIdGet: true);
if(!empty($errorApi)){
    array_push($response->message, $errorApi);
    echo json_encode($response);
    return;
}
$id= $_GET["id"];
// validacion de email y password que esten presentes

$error_messages = validarInfo($json, ['nombre', 'email']);
if($error_messages != null && count($error_messages) > 0){
    array_push($response->message, ...$error_messages);
    $error = true;
} else if(!filter_var($json->email, FILTER_VALIDATE_EMAIL)){
    array_push($response->message, "email debe tener un formato valido ej: example@example.com");
    $error = true;
}
// valida si hay una nueva password establecida
if(!empty($json->newpassword)){
    if (empty($json->confirm_newpassword)){
        array_push($response->message, "confirm_newpassword es requerida");
        $error = true;
    } else if($json->newpassword != $json->confirm_newpassword){
        array_push($response->message, "newpassword y confirm_newpassword deben ser iguales");
        $error = true;
    }    
}

$userService = new UsuarioService();
// si no hay errores aplica esta validaciones adicionales
if(!$error){
    $user = $userService->get_by_id($id);

    // valida si existe el usuario con el id a modificar
    if($user == null){
        array_push($response->message, "no se encontro usuario con id: $id");
        $error = true;
    } else {
        // valida si el nuevo email existe en base de datos
        $exists = ($json->email != $user->email) ? $userService->exist_email($json->email) : false;
        if($exists){
            array_push($response->message, "El nuevo email ya se encuentra registrado");
            $error = true;
        }   
    }
}
// si hay errores los muestra en pantalla en formato json
if($error){
    echo json_encode($response);
    return;
}

// crear el nuevo registro y valida que sea exitoso
$result = $userService->actualizar($id, $json);
if(!$result){
    array_push($response->message, 'Hubo un problema al realizar el registro');
} else {
    $response->code = 0;
    array_push($response->message, 'Registro de nuevo usuario satisfactorio');
}
echo json_encode($response);