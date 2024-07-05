<?php
// Incluir el controlador de PersonaNatural
require_once '../src/controllers/PersonaNaturalController.php';
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
$controlador = new PersonaNaturalController();

// Verificar el método de solicitud HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear una nueva persona natural
    $idNuevaPersona = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevaPersona]); // Devuelve el ID de la nueva persona creada
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Actualizar la información de una persona natural existente
    parse_str(file_get_contents("php://input"), $datos); // Obtener los datos de la solicitud PUT
    $idPersonaExistente = $datos['idUsuario']; // Obtener el ID de la persona a actualizar
    $actualizacionExitosa = $controlador->actualizar($idPersonaExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]); // Devuelve true si la actualización fue exitosa
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['idUsuario'])) {
        $id = $_GET['idUsuario'];
        $persona = $controlador->obtenerPorIdUsuario($id);
        if ($persona) {
            // Establecer el encabezado de respuesta JSON
            header('Content-Type: application/json');
            echo json_encode($persona);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Persona no encontrada."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ID de persona no proporcionado."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>