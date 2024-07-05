<?php
// Incluir el controlador de PersonaNatural
require_once '../src/controllers/InfoNucleoFamiliarController.php';
require_once '../auth/verifyToken.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

if ($decodedToken === null) {
    http_response_code(401);
    echo json_encode(array("message" => "Token no válido o no proporcionado."));
    exit(); // Terminar la ejecución si el token no es válido
}

// Crear una instancia del controlador
$controlador = new InformacionNucleoFamiliarController();

// Verificar el método de solicitud HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = $_POST;
    $idNuevo = $controlador->crear($datos);
    echo json_encode(['id' => $idNuevo]); // Devuelve el ID de la nueva persona creada
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idExistente = $datos['id']; // Obtener el ID de la persona a actualizar
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]); // Devuelve true si la actualización fue exitosa
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $usuario = $controlador->obtenerPorId($id);
        if ($usuario) {
            // Establecer el encabezado de respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } elseif (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $usuario = $controlador->obtenerPorIdUsuario($id);
        if ($usuario) {
            // Establecer el encabezado de respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($usuario);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Información no encontrada."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID no proporcionado."));
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