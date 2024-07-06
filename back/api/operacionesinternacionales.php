<?php

require_once __DIR__ . '/../src/controllers/OperacionesInternacionalesController.php';
require_once '../auth/verifyToken.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit(); // Terminar la ejecución si el token no es válido
}

$controlador = new OperacionesInternacionalesController();

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
            echo json_encode(array("message" => "Operación internacional no encontrada."));
        }
    } elseif(isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $operacion = $controlador->obtenerPorIdUsuario($id);
        if ($operacion) {
            header('Content-Type: application/json');
            echo json_encode($operacion);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Operación internacional no encontrada."));
        }
    } else {
        $operaciones = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($operaciones);
    }
} elseif($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el ID del registro a eliminar
    parse_str(file_get_contents("php://input"), $datos);
    $idEliminar = $datos['id']; // Obtener el ID de la información familiar a eliminar

    $eliminacionExitosa = $controlador->eliminar($idEliminar);
    echo json_encode(['success' => $eliminacionExitosa]);
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>