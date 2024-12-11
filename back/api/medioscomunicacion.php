<?php

require_once '../src/controllers/MedioComunicacionController.php';

$controlador = new MedioComunicacionController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $medioComunicacion = $controlador->obtenerPorId($id);
        if ($medioComunicacion) {
            header('Content-Type: application/json');
            echo json_encode($medioComunicacion);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Medio de comunicación no encontrado."));
        }
    } else {
        $mediosComunicacion = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($mediosComunicacion);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>