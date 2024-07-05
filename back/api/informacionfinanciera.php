<?php

require_once '../src/controllers/InfoFinancieraController.php';
require_once '../auth/verifyToken.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit(); // Terminar la ejecución si el token no es válido
}

$controlador = new InfoFinancieraController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevo = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevo]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $infoFinanc = $controlador->obtenerPorIdUsuario($id);
        if ($infoFinanc) {
            header('Content-Type: application/json');
            echo json_encode($infoFinanc);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información Financiera no encontrada."));
        }
    } else {
        $infoFinanc = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($infoFinanc);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>