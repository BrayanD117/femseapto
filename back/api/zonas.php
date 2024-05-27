<?php

require_once '../src/controllers/ZonaGeograficaController.php';

$controlador = new ZonaGeograficaController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $zonaGeo = $controlador->obtenerPorId($id);
        if ($zonaGeo) {
            header('Content-Type: application/json');
            echo json_encode($zonaGeo);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Género no encontrado."));
        }
    } else {
        $zonasGeo = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($zonasGeo);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>