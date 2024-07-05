<?php

require_once '../src/controllers/DepartamentoController.php';

$controlador = new DepartamentoController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $dpto = $controlador->obtenerPorId($id);
        if ($dpto) {
            header('Content-Type: application/json');
            echo json_encode($dpto);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Departamento no encontrado."));
        }
    } else {
        $departamentos = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($departamentos);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>