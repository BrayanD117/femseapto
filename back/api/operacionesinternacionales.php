<?php

require_once __DIR__ . '/../src/controllers/OperacionesInternacionalesController.php';

$controlador = new OperacionesInternacionalesController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevaOperacion = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevaOperacion]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
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
