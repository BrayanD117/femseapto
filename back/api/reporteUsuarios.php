<?php

require_once '../src/controllers/ReporteUsuarioController.php';
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getReporteUsuarios();
} else {
    http_response_code(405);
    $response = [
        'success' => false,
        'message' => 'Método no permitido',
    ];
    echo json_encode($response);
}

?>
