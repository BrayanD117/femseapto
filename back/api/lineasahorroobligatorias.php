<?php

require_once '../src/controllers/LineaAhorroObligatoriaController.php';

$controlador = new LineaAhorroObligatoriaController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resp = $controlador->obtenerPorId($id);
        if ($resp) {
            header('Content-Type: application/json');
            echo json_encode($resp);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "País no encontrado."));
        }
    } else {
        $resp = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($resp);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>