<?php

require_once __DIR__ . '/../src/controllers/PersonaExpuestaPublicamenteController.php';
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

$controlador = new PersonaExpuestaPublicamenteController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //$datos = $_POST;
    $datos = json_decode(file_get_contents("php://input"), true);
    $idNuevaOperacion = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevaOperacion]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $operacion = $controlador->obtenerPorId($id);
        if ($operacion) {
            header('Content-Type: application/json');
            echo json_encode($operacion);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } elseif(isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $operacion = $controlador->obtenerPorIdUsuario($id);
        if ($operacion) {
            header('Content-Type: application/json');
            echo json_encode($operacion);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } elseif (isset($_GET['val'])) {
        $id = $_GET['val'];
        $infoFinanc = $controlador->validarPersonaPublica($id);
        if ($infoFinanc) {
            header('Content-Type: application/json');
            echo json_encode($infoFinanc);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } else {
        $operaciones = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($operaciones);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>