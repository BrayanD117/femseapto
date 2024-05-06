<?php
// Incluir el controlador de PersonaNatural
require_once '../controllers/PersonaNaturalController.php';

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
        $persona = $controlador->obtenerPorId($id);
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
