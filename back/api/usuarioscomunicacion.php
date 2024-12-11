<?php
require_once '../src/controllers/UsuarioComunicacionController.php';
require_once '../auth/verifyToken.php';
require_once '../config/cors.php';

$key = $_ENV['JWT_SECRET_KEY'];
$token = $_COOKIE['auth_token'] ?? '';

$decodedToken = verifyJWTToken($token, $key);

// if ($decodedToken === null) {
//     http_response_code(401);
//     echo json_encode(["message" => "Token no válido o no proporcionado."]);
//     exit();
// }

$controlador = new UsuarioComunicacionController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = json_decode(file_get_contents("php://input"), true);
    $idUsuario = $datos['idUsuario'] ?? null;
    $idsMedios = $datos['idsMedios'] ?? [];

    if (!$idUsuario || !is_array($idsMedios)) {
        http_response_code(400);
        echo json_encode(["message" => "Datos incompletos o inválidos."]);
        exit();
    }

    try {
        $controlador->sincronizarMedios($idUsuario, $idsMedios);
        http_response_code(200);
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["message" => "Ocurrió un error al sincronizar los datos.", "error" => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $usuarioComunicaciones = $controlador->obtenerPorIdUsuario($id);
        if ($usuarioComunicaciones) {
            header('Content-Type: application/json');
            echo json_encode($usuarioComunicaciones);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Persona no encontrada."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID de usuario no proporcionado."));
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Método no permitido."]);
}
?>