<?php
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/servicios/usuarioService.php';
header("Content-Type: application/json");
$response = new stdClass;
$response->code = -1;
$response->message = [];
$error = false;

// validacion del api metodo que sea DELETE
$errorApi = validarApi(null, "DELETE", validarIdGet: true, validarJson: false);
if(!empty($errorApi)){
    array_push($response->message, $errorApi);
    echo json_encode($response);
    return;
}

$id = intval($_GET['id']);

$userService = new UsuarioService();

if(!$error){
    $user = $userService->get_by_id($_GET["id"]);
    if($user == null){
        array_push($response->message, "El usuario con id $id no existe");
        $error = true;
    }
}

if($error){
    echo json_encode($response);
    return;
}

// se valida el usuario y password para mirar si existe  y sino mostrar mensaje
$result = $userService->remove_usuario($id);
if(!$result){
    array_push($response->message, 'Hubo un error al remover el usuario');
} else {
    $response->code = 0;
    array_push($response->message, 'Usuario removido exitosamente');
}
echo json_encode($response);