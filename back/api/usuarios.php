<?php

require_once '../src/controllers/UsuarioController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null || !isset($decodedToken->userId)) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit();
}

$idUsuario = $decodedToken->userId;
// Crear una instancia del controlador
$controlador = new UsuarioController();

// Verificar el método de solicitud HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['changePassword']) && $_GET['changePassword'] === 'true') {
    $datos = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($datos['currentPassword']) || !isset($datos['newPassword'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos para cambiar la contraseña."));
        exit();
    }

    $currentPassword = $datos['currentPassword'];
    $newPassword = $datos['newPassword'];

    $response = $controlador->cambiarContrasenia($idUsuario, $currentPassword, $newPassword);
    header('Content-Type: application/json');
    echo json_encode($response);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['updatePrimerIngreso']) && $_GET['updatePrimerIngreso'] === 'true') {
    $datos = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($datos['userId']) || !isset($datos['primerIngreso'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Datos incompletos para actualizar el primer ingreso."));
        exit();
    }

    $userId = $datos['userId'];
    $primerIngreso = $datos['primerIngreso'];

    $usuario = $controlador->obtenerPorId($userId);
    if ($usuario) {
        $usuario->primerIngreso = $primerIngreso;
        $usuario->guardar();
        http_response_code(200);
        echo json_encode(array("message" => "Primer ingreso actualizado correctamente."));
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "Usuario no encontrado."));
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevo = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];

    if (isset($datos['restablecerContrasenia']) && $datos['restablecerContrasenia'] === true) {
        $actualizacionExitosa = $controlador->restablecerContrasenia($idExistente);
        echo json_encode(['success' => $actualizacionExitosa]);
    } else {
        $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
        echo json_encode(['success' => $actualizacionExitosa]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $usuario = $controlador->obtenerPorId($id);
        if ($usuario) {
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Persona no encontrada."));
        }
    } else {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
        $idRol = isset($_GET['idRol']) ? (int)$_GET['idRol'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;

        $resp = $controlador->obtenerConPaginacion($page, $size, $idRol, $search);
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
        $resultado = $controlador->cambiarEstadoActivo($id);
        if ($resultado) {
            echo json_encode(array("message" => "Estado del usuario actualizado exitosamente."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID del usuario no proporcionado."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>