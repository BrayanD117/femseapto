<?php

require_once '../src/controllers/SolicitudAhorroController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit();
}

$controlador = new SolicitudAhorroController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevo = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resp = $controlador->obtenerPorId($id);
        if ($resp) {
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Solicitud de ahorro no encontrada."));
        }
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $resp = $controlador->obtenerPorIdUsuario($id);
        if ($resp) {
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Solicitudes de ahorro no encontradas."));
        }
    } else {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $resp = $controlador->obtenerConPaginacion($page, $size, $search);
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}

?>