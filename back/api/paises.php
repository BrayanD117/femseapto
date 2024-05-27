<?php

require_once '../src/controllers/PaisController.php';

$controlador = new PaisController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $pais = $controlador->obtenerPorId($id);
        if ($pais) {
            header('Content-Type: application/json');
            echo json_encode($pais);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "País no encontrado."));
        }
    } else {
        $paises = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($paises);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>