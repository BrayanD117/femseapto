<?php

require_once '../src/controllers/UsuarioController.php';
require_once '../auth/verifyToken.php';
require_once '../config/config.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null || !isset($decodedToken->userId)) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents('php://input'), true);
    $controller = new UsuarioController();
    echo json_encode($controller->obtenerDatosCompletoUsuarios($datos));
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}