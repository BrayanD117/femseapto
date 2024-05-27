<?php
require_once '../src/controllers/LineaCreditoController.php';

$controlador = new LineaCreditoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idNuevaLineaCredito = $controlador->crear($_POST);
    echo json_encode(['id' => $idNuevaLineaCredito]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    parse_str(file_get_contents("php://input"), $datos);
    $idExistente = $datos['id'];
    $actualizacionExitosa = $controlador->actualizar($idExistente, $datos);
    echo json_encode(['success' => $actualizacionExitosa]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $lineaCredito = $controlador->obtenerPorId($id);
        if ($lineaCredito) {
            header('Content-Type: application/json');
            echo json_encode($lineaCredito);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Línea de crédito no encontrada."));
        }
    } else {
        $lineasCredito = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($lineasCredito);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>
