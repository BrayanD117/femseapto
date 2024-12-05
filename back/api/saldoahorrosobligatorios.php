<?php

require_once '../src/controllers/SaldoAhorroObligatorioController.php';
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

$controlador = new SaldoAhorroObligatorioController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controlador->upload();
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resp = $controlador->obtenerPorId($id);
        echo json_encode($resp);
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $resp = $controlador->obtenerPorIdUsuario($id);
        echo json_encode($resp);
    } else {
        $resp = $controlador->obtenerTodos();
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}