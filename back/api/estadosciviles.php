<?php

require_once '../src/controllers/EstadoCivilController.php';

$controlador = new EstadoCivilController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $estadoCivil = $controlador->obtenerPorId($id);
        if ($estadoCivil) {
            header('Content-Type: application/json');
            echo json_encode($estadoCivil);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Estado civil no encontrado."));
        }
    } else {
        $estadosCiviles = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($estadosCiviles);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>