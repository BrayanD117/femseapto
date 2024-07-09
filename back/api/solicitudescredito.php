<?php

require_once '../src/controllers/SolicitudCreditoController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit(); // Terminar la ejecución si el token no es válido
}

// Crear una instancia del controlador
$controlador = new SolicitudCreditoController();

// Verificar el método de solicitud HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevo = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevo]); // Devuelve el ID
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id']; // Obtener el ID
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]); // Devuelve true si la actualización fue exitosa
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resp = $controlador->obtenerPorId($id);
        if ($resp) {
            // Establecer el encabezado de respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Solicitud de crédito no encontrada."));
        }
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $resp = $controlador->obtenerPorIdUsuario($id);
        if ($resp) {
            // Establecer el encabezado de respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Tipo de vinculación no encontrada."));
        }
    } else {
        $resp = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>