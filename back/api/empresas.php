<?php

require_once '../src/controllers/EmpresaController.php';

$controlador = new EmpresaController();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $empresa = $controlador->obtenerPorId($id);
        if ($empresa) {
            header('Content-Type: application/json');
            echo json_encode($empresa);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Empresa no encontrada."));
        }
    } else {
        $empresas = $controlador->obtenerTodos();
        header('Content-Type: application/json');
        echo json_encode($empresas);
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Método no permitido."));
}
?>